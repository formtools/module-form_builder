<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\General as CoreGeneral;
use PDO, Exception;


class Forms
{
    /**
     * Returns information about a form configuration.
     *
     * @param $published_form_id
     */
    public static function getFormConfiguration($published_form_id)
    {
        $db = Core::$db;

        if (empty($published_form_id) || !is_numeric($published_form_id)) {
            return array();
        }

        $db->query("
            SELECT * FROM {PREFIX}module_form_builder_forms
            WHERE published_form_id = :form_id
        ");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        $result = $db->fetch();

        if (empty($result)) {
            return array();
        }

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_form_templates
            WHERE  published_form_id = :form_id
        ");
        $db->bind("form_id", $published_form_id);
        $db->execute();
        $result["templates"] = $db->fetchAll();

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_form_placeholders
            WHERE  published_form_id = :form_id
        ");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        $result["placeholders"] = $db->fetchAll();

        return $result;
    }


    /**
     * Returns all published versions of a form.
     */
    public static function getPublishedForms($form_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_forms
            WHERE  form_id = :form_id
            ORDER BY list_order
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        $configured_forms = $db->fetchAll();

        $db->query("
            SELECT count(*) 
            FROM   {PREFIX}module_form_builder_forms
        ");
        $db->execute();

        return array(
            "results" => $configured_forms,
            "num_results" => $db->fetch(PDO::FETCH_COLUMN)
        );
    }



    public static function updateConfiguredFormTemplates($published_form_id, $info, $L)
    {
        $db = Core::$db;

        $db->query("
            UPDATE {PREFIX}module_form_builder_form_templates
            SET set_id = :set_id
            WHERE published_form_id = :form_id
        ");
        $db->bindAll(array(
            "set_id" => $info["template_set_id"],
            "form_id" => $published_form_id
        ));
        $db->execute();

        $db->query("
            DELETE FROM {PREFIX}module_form_builder_form_templates
            WHERE published_form_id = :form_id
        ");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        $template_data = array(
            "page_layout"    => $info["page_layout_template_id"],
            "header"         => $info["header_template_id"],
            "footer"         => $info["footer_template_id"],
            "navigation"     => $info["navigation_template_id"],
            "form_page"      => $info["form_page_template_id"],
            "review_page"    => $info["review_page_template_id"],
            "thankyou_page"  => $info["thankyou_page_template_id"],
            "continue_block" => $info["continue_block_template_id"],
            "error_message"  => $info["error_message_template_id"]
        );

        while (list($key, $template_id) = each($template_data)) {
            $db->query("
                INSERT INTO {PREFIX}module_form_builder_form_templates (published_form_id, template_type, template_id)
                VALUES (:form_id, :template_type, :template_id)
            ");
            $db->bindAll(array(
                "form_id" => $published_form_id,
                "template_type" => $key,
                "template_id" => $template_id
            ));
            $db->execute();
        }

        return array(true, $L["notify_template_set_templates_updated"]);
    }


    public static function deleteFormConfiguration($form_id, $published_form_id, $L)
    {
        $db = Core::$db;

        $db->query("DELETE FROM {PREFIX}module_form_builder_forms WHERE published_form_id = :form_id");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_form_builder_form_placeholders WHERE published_form_id = :form_id");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_form_builder_form_templates WHERE published_form_id = :form_id");
        $db->bind("form_id", $published_form_id);
        $db->execute();

        self::updatePublishedFormOrder($form_id, $L);

        return array(true, $L["notify_form_configuration_deleted"]);
    }


    /**
     * Deletes a published form and, optionally, the form configuration as well.
     *
     * @param integer $form_id
     * @param integer $published_form_id
     * @param string $delete_form_config "yes" / "no"
     * @param boolean $override
     */
    public static function deletePublishedForm($form_id, $published_form_id, $delete_form_config, $L, $override = false)
    {
        $db = Core::$db;

        $config = self::getFormConfiguration($published_form_id);
        if (empty($config)) {
            return array(false, $L["notify_delete_form_config_not_found"]);
        }

        $folder_path = $config["folder_path"];
        $filename    = $config["filename"];

        // see if the file exists (only bother if the user isn't overriding)
        if (!$override) {
            $file = "$folder_path/$filename";
            if (!is_file($file)) {
                $ignore_link = "?page=publish&delete_published_form=$published_form_id&delete_form_config=$delete_form_config&override=1";
                $params = array(
                    "file"        => $file,
                    "ignore_link" => $ignore_link
                );
                $message = CoreGeneral::evalSmartyString($L["notify_form_missing_cannot_delete"], $params);
                return array(false, $message);
            }

            // this probably isn't necessary, but it doesn't hurt: change the permissions on the file to 777
            @chmod($file, 0777);
            if (!@unlink($file)) {
                array(false, $L["notify_cannot_delete_form_file"]);
            }
        }

        if ($delete_form_config == "yes") {
            self::deleteFormConfiguration($form_id, $published_form_id, $L);
        } else {
            // update the form configuration to make a note of the fact that it's no longer published
            $db->query("
                UPDATE {PREFIX}module_form_builder_forms
                SET    is_published = 'no',
                       publish_date = NULL,
                       filename = '',
                       folder_path = '',
                       folder_url = ''
                WHERE  published_form_id = :form_id
            ");
            $db->bindAll(array(
                "form_id" => $published_form_id
            ));
            $db->execute();
        }

        return array(true, $L["notify_published_form_deleted"]);
    }


    /**
     * Helper function to build a list of pages in the form. This is used for generating the list of visible
     * pages in the Form Builder, and in the actual generated forms.
     *
     * @param $view_id
     * @param $include_review_page
     * @param return an array of hashes; each hash contains information about the page. It has the following keys:
     *    page_name (the tab name)
     *    page_type (form, review, thanks)
     */
    public static function getNavPages($params)
    {
        $view_tabs                  = $params["view_tabs"];
        $include_review_page        = $params["include_review_page"];
        $include_thanks_page_in_nav = $params["include_thanks_page_in_nav"];
        $review_page_title          = $params["review_page_title"];
        $thankyou_page_title        = $params["thankyou_page_title"];

        $pages = array();
        if (!empty($view_tabs)) {
            $count = 1;
            foreach ($view_tabs as $tab_label) {
                $pages[] = array(
                    "page_name" => $tab_label["tab_label"],
                    "page_type" => "form"
                );
                $count++;
            }
        } else {
            $pages[] = array(
                "page_name" => "Form",
                "page_type" => "form"
            );
        }

        if ($include_review_page) {
            $pages[] = array(
                "page_name" => $review_page_title,
                "page_type" => "review"
            );
        }

        if ($include_thanks_page_in_nav) {
            $pages[] = array(
                "page_name" => $thankyou_page_title,
                "page_type" => "thanks"
            );
        }

        return $pages;
    }


    /**
     * A sister function to fb_get_nav_pages() above. This returns all pages in the form, including the review +
     * thankyou page - the Review page isn't included if need be, but the Thankyou page always is.
     *
     * @param array $params
     * @return array $pages
     */
    public static function getAllFormPages($params)
    {
        $view_tabs           = $params["view_tabs"];
        $include_review_page = $params["include_review_page"];
        $review_page_title   = $params["review_page_title"];
        $thankyou_page_title = $params["thankyou_page_title"];

        $pages = array();
        if (!empty($view_tabs)) {
            $count = 1;
            foreach ($view_tabs as $tab_label) {
                $pages[] = array(
                    "page_name" => $tab_label["tab_label"],
                    "page_type" => "form"
                );
                $count++;
            }
        } else {
            $pages[] = array(
                "page_name" => "Form",
                "page_type" => "form"
            );
        }

        if ($include_review_page) {
            $pages[] = array(
                "page_name" => $review_page_title,
                "page_type" => "review"
            );
        }

        $pages[] = array(
            "page_name" => $thankyou_page_title,
            "page_type" => "thanks"
        );

        return $pages;
    }


    /**
     * This figures out the page type (form, review, thanks) for an online form. If the page number being
     * passed isn't valid, it returns false.
     *
     * @return mixed "form_page", "review_page", "thanks_page" or false if the page number is invalid
     */
    public static function getCurrentPageType($page_num, $view_tabs, $include_review_page)
    {
        $total_num_pages = count($view_tabs) + 1;
        if ($include_review_page) {
            $total_num_pages++;
        }

        if (!is_numeric($page_num) || $page_num < 1 || $page_num > $total_num_pages) {
            return false;
        }

        $page_type = "form_page";
        if ($page_num == $total_num_pages) {
            $page_type = "thanks_page";
        } else if ($include_review_page && $page_num == $total_num_pages-1) {
            $page_type = "review_page";
        }

        return $page_type;
    }


    /**
     * This actually creates the form file.
     */
    public static function publishForm($info, $L)
    {
        $db = Core::$db;

        $published_form_id = $info["published_form_id"];
        $filename          = $info["publish_filename"] . ".php";
        $folder_path       = $info["publish_folder_path"];
        $folder_url        = $info["publish_folder_url"];

        if (!is_dir($folder_path)) {
            return array(
                "success" => false,
                "message" => $L["notify_folder_path_not_folder"]
            );
        }

        if (!is_writable($folder_path)) {
            return array(
                "success" => false,
                "message" => $L["notify_folder_path_not_writable"]
            );
        }

        // don't allow overwriting of existing files
        if (is_file("$folder_path/$filename")) {
            return array(
                "success" => false,
                "message" => $L["notify_file_already_exists"]
            );
        }

        $folder_path = preg_replace("/\/$/", "", $folder_path);
        $folder_url  = preg_replace("/\/$/", "", $folder_url);

        $content = Forms::getGeneratedFormContent($published_form_id, $filename);
        $file = $folder_path . "/" . $filename;
        if ($fh = fopen($file, 'w')) {
            fwrite($fh, $content);
            fclose($fh);
            $publish_date = CoreGeneral::getCurrentDatetime();
            $url = $folder_url . "/" . $filename;

            $db->query("
                UPDATE {PREFIX}module_form_builder_forms
                SET    is_published = 'yes',
                       publish_date = :publish_date,
                       filename = :filename,
                       folder_path = :folder_path,
                       folder_url = :folder_url
                WHERE  published_form_id = :published_form_id
            ");
            $db->bindAll(array(
                "publish_date" => $publish_date,
                "filename" => $filename,
                "folder_path" => $folder_path,
                "folder_url" => $folder_url,
                "published_form_id" => $published_form_id
            ));
            $db->execute();

            return array(
                "success"     => true,
                "url"         => $url,
                "filename"    => $filename,
                "folder_path" => $folder_path,
                "folder_url"  => $folder_url
            );
        }

        return array(
            "success" => false,
            "message" => $L["notify_general_error_creating_form"]
        );
    }


    public static function updatePublishSettings($info, $L)
    {
        $db = Core::$db;

        $published_form_id = $info["published_form_id"];
        $new_filename      = trim($info["new_publish_filename"]) . ".php";
        $old_filename      = trim($info["old_publish_filename"]) . ".php";
        $new_folder_path   = trim($info["new_publish_folder_path"]);
        $old_folder_path   = trim($info["old_publish_folder_path"]);
        $new_folder_url    = trim($info["new_publish_folder_url"]);

        // first, verify the new folder + file settings
        if (!is_dir($new_folder_path)) {
            return array(
                "success" => false,
                "message" => $L["notify_folder_path_not_folder"]
            );
        }

        if (!is_writable($new_folder_path)) {
            return array(
                "success" => false,
                "message" => $L["notify_folder_path_not_writable"]
            );
        }

        // don't allow overwriting of existing files
        if (is_file("$new_folder_path/$new_filename")) {
            return array(
                "success" => false,
                "message" => $L["notify_file_already_exists"]
            );
        }

        // all's good! Now delete the older file. If it can't be done (for ANY reason), we inform the user and give them
        // the option just to ignore the problem (which is indicated by "override" being passed as true)
        if ($info["override"] == "false") {
            $params = array(
                "old_file" => "$old_folder_path/$old_filename",
                "onclick"  => "return builder_js.overide_publish_settings()"
            );
            $notify_previous_file_not_exist = CoreGeneral::evalSmartyString($L["notify_previous_file_not_exist"], $params);
            if (!is_file("$old_folder_path/$old_filename")) {
                return array(
                    "success" => false,
                    "message" => $notify_previous_file_not_exist
                );
            } else {
                $result = @unlink("$old_folder_path/$old_filename");
                if (!$result) {
                    return array(
                        "success" => false,
                        "message" => $notify_previous_file_not_exist
                    );
                }
            }
        }

        $new_folder_path = preg_replace("/\/$/", "", $new_folder_path);
        $new_folder_url  = preg_replace("/\/$/", "", $new_folder_url);
        $content = self::getGeneratedFormContent($published_form_id, $new_filename);
        $file = $new_folder_path . "/" . $new_filename;

        if ($fh = fopen($file, 'w')) {
            fwrite($fh, $content);
            fclose($fh);
            $publish_date = CoreGeneral::getCurrentDatetime();
            $url = $new_folder_url . "/" . $new_filename;

            $db->query("
                UPDATE {PREFIX}module_form_builder_forms
                SET    is_published = 'yes',
                       publish_date = :publish_date,
                       filename = :filename,
                       folder_path = :folder_path,
                       folder_url = :folder_url
                WHERE  published_form_id = :published_form_id
            ");
            $db->bindAll(array(
                "publish_date" => $publish_date,
                "filename" => $new_filename,
                "folder_path" => $new_folder_path,
                "folder_url" => $new_folder_url,
                "published_form_id" => $published_form_id
            ));
            $db->execute();

            return array(
                "success" => true,
                "url"     => $url
            );
        }

        return array(
            "success" => false,
            "message" => $L["notify_general_error_creating_form"]
        );
    }


    /**
     * Updates the values entered into placeholders for a form.
     *
     * @param $published_form_id
     * @param $info
     */
    public static function updateFormPlaceholders($published_form_id, $info, $L)
    {
        $db = Core::$db;

        $placeholder_ids = $info["placeholder_ids"];

        $db->query("
            DELETE FROM {PREFIX}module_form_builder_form_placeholders
            WHERE published_form_id = :published_form_id
        ");
        $db->bind("published_form_id", $published_form_id);
        $db->execute();

        foreach ($placeholder_ids as $pid) {
            if (!isset($info["placeholder_{$pid}"])) {
                continue;
            }

            if (is_array($info["placeholder_{$pid}"])) {
                $value = implode("|", $info["placeholder_{$pid}"]);
            } else {
                $value = $info["placeholder_{$pid}"];
            }

            $db->query("
                INSERT INTO {PREFIX}module_form_builder_form_placeholders (published_form_id, placeholder_id, placeholder_value)
                VALUES (:published_form_id, :placeholder_id, :placeholder_value)
            ");
            $db->bindAll(array(
                "published_form_id" => $published_form_id,
                "placeholder_id" => $pid,
                "placeholder_value" => $value
            ));
            $db->execute();
        }

        return array(true, $L["notify_form_placeholders_updated"]);
    }


    /**
     * Called in the Edit Form -> Publish tab. This converts an Internal or External form to Form Builder form.
     *
     * @param integer $form_id
     */
    public static function convertFormToFormBuilderForm($form_id, $L)
    {
        $db = Core::$db;

        $db->query("
            UPDATE {PREFIX}forms
            SET    form_type = 'form_builder'
            WHERE  form_id = :form_id
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return array(true, $L["notify_form_converted_to_form_builder"]);
    }


    /**
     * Returns the last number + 1 for new template set creation.
     *
     * @return integer
     */
    public static function getNextPublishedFormOrder($form_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT list_order
            FROM   {PREFIX}module_form_builder_forms
            WHERE  form_id = :form_id
            ORDER BY list_order DESC
            LIMIT 1
        ");
        $db->bind("form_id", $form_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) + 1;
    }


    /**
     * Called after the user deletes a published form. It updates the order of the remaining
     * published forms. Also called by the administrator when re-sorting the published forms listed on the Edit
     * Form -> Publish tab.
     *
     * @param integer $form_id
     * @param array $info
     */
    public static function updatePublishedFormOrder($form_id, $L, $info = array())
    {
        $db = Core::$db;

        if (empty($info)) {
            $db->query("
                SELECT published_form_id
                FROM   {PREFIX}module_form_builder_forms
                WHERE  form_id = :form_id
                ORDER BY list_order ASC
            ");
            $db->bind("form_id", $form_id);
            $db->execute();

            $new_list_order = 1;
            $published_form_ids = $db->fetchAll(PDO::FETCH_COLUMN);
            foreach ($published_form_ids as $published_form_id) {
                $db->query("
                    UPDATE {PREFIX}module_form_builder_forms
                    SET    list_order = :list_order
                    WHERE  published_form_id = :published_form_id
                ");
                $db->bindAll(array(
                    "list_order" => $new_list_order,
                    "published_form_id" => $published_form_id
                ));
                $db->execute();

                $new_list_order++;
            }
        } else {
            $sortable_id = "form_builder_form_list";
            $published_form_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

            $order = 1;
            foreach ($published_form_ids as $published_form_id) {
                $db->query("
                    UPDATE {PREFIX}module_form_builder_forms
                    SET    list_order = :list_order
                    WHERE  published_form_id = :published_form_id
                ");

                $db->bindAll(array(
                    "list_order" => $order,
                    "published_form_id" => $published_form_id
                ));
                $db->execute();

                $order++;
            }
        }

        return array(true, $L["notify_published_forms_updated"]);
    }

    /**
     * Called when the user clicks the "Save" button in the Builder popup.
     * TODO split this to update/create & rename... "saveBuilderSettings...?"
     * @param array $info
     */
    public static function saveBuilderSettings($info)
    {
        $db = Core::$db;

        // optional. If this is set, the user is updating an existing published form
        $published_form_id = !empty($info["published_form_id"]) ? $info["published_form_id"] : "";
        $is_online = (isset($info["is_online"])) ? "yes" : "no";
        $include_review_page        = isset($info["include_review_page"]) ? "yes" : "no";
        $include_thanks_page_in_nav = isset($info["include_thanks_page_in_nav"]) ? "yes" : "no";

        $offline_date = null;
        if (!empty($info["offline_date"]) && preg_match("/\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}/", $info["offline_date"])) {
            list($date, $time) = explode(" ", $info["offline_date"]);
            list($month, $day, $year) = explode("/", $date);
            $offline_date = "{$year}-{$month}-{$day} $time";
        }

        $filename = $info["filename"];
        if (!preg_match("/\.php$/", $filename)) {
            $filename .= ".php";
        }

        try {

            if (empty($published_form_id)) {
                $list_order = self::getNextPublishedFormOrder($info["form_id"]);

                $db->query("
                    INSERT INTO {PREFIX}module_form_builder_forms (is_online, is_published, form_id, view_id,
                        set_id, filename, folder_path, folder_url, include_review_page, include_thanks_page_in_nav,
                        thankyou_page_content, form_offline_page_content, review_page_title, thankyou_page_title, list_order,
                        offline_date)
                    VALUES (:is_online, :is_published, :form_id, :view_id, :set_id, :filename, :folder_path, :folder_url,
                        :include_review_page, :include_thanks_page_in_nav, :thankyou_page_content, :form_offline_page_content,
                        :review_page_title, :thankyou_page_title, :list_order, :offline_date)
                ");
                $db->bindAll(array(
                    "is_online" => $is_online,
                    "is_published" => "no",
                    "form_id" => $info["form_id"],
                    "view_id" => $info["view_id"],
                    "set_id" => $info["template_set_id"],
                    "filename" => $filename,
                    "folder_path" => $info["folder_path"],
                    "folder_url" => $info["folder_url"],
                    "include_review_page" => $include_review_page,
                    "include_thanks_page_in_nav" => $include_thanks_page_in_nav,
                    "thankyou_page_content" => $info["thankyou_page_content"],
                    "form_offline_page_content" => $info["form_offline_page_content"],
                    "review_page_title" => $info["review_page_title"],
                    "thankyou_page_title" => $info["thankyou_page_title"],
                    "list_order" => $list_order,
                    "offline_date" => $offline_date
                ));
                $db->execute();
                $published_form_id = $db->getInsertId();
            } else {
                $db->query("
                    UPDATE {PREFIX}module_form_builder_forms
                    SET    is_online = :is_online,
                           form_id = :form_id,
                           view_id = :view_id,
                           set_id = :set_id,
                           filename = :filename,
                           folder_path = :folder_path,
                           folder_url = :folder_url,
                           include_review_page = :include_review_page,
                           include_thanks_page_in_nav = :include_thanks_page_in_nav,
                           thankyou_page_content = :thankyou_page_content,
                           form_offline_page_content = :form_offline_page_content,
                           review_page_title = :review_page_title,
                           thankyou_page_title = :thankyou_page_title,
                           offline_date = :offline_date
                    WHERE  published_form_id = :published_form_id
                ");
                $db->bindAll(array(
                    "is_online" => $is_online,
                    "form_id" => $info["form_id"],
                    "view_id" => $info["view_id"],
                    "set_id" => $info["template_set_id"],
                    "filename" => $filename,
                    "folder_path" => $info["folder_path"],
                    "folder_url" => $info["folder_url"],
                    "include_review_page" => $include_review_page,
                    "include_thanks_page_in_nav" => $include_thanks_page_in_nav,
                    "thankyou_page_content" => $info["thankyou_page_content"],
                    "form_offline_page_content" => $info["form_offline_page_content"],
                    "review_page_title" => $info["review_page_title"],
                    "thankyou_page_title" => $info["thankyou_page_title"],
                    "offline_date" => $offline_date,
                    "published_form_id" => $published_form_id
                ));
                $db->execute();
            }
        } catch (Exception $e) {
            return array(false, $e->getMessage());
        }

        $db->query("
            DELETE FROM {PREFIX}module_form_builder_form_templates
            WHERE published_form_id = :published_form_id
        ");
        $db->bind("published_form_id", $published_form_id);
        $db->execute();

        $template_data = array(
            "page_layout"       => $info["page_layout_template_id"],
            "header"            => $info["header_template_id"],
            "footer"            => $info["footer_template_id"],
            "navigation"        => $info["navigation_template_id"],
            "continue_block"    => $info["continue_block_template_id"],
            "error_message"     => $info["error_message_template_id"],
            "form_page"         => $info["form_page_template_id"],
            "review_page"       => $info["review_page_template_id"],
            "thankyou_page"     => $info["thankyou_page_template_id"],
            "form_offline_page" => $info["form_offline_page_template_id"]
        );

        while (list($key, $template_id) = each($template_data)) {
            $db->query("
                INSERT INTO {PREFIX}module_form_builder_form_templates (published_form_id, template_type, template_id)
                VALUES (:published_form_id, :template_type, :template_id)
	        ");
            $db->bindAll(array(
                "published_form_id" => $published_form_id,
                "template_type" => $key,
                "template_id" => $template_id
            ));
            $db->execute();
        }

        // now add the placeholders
        $placeholder_ids = (isset($info["placeholder_ids"])) ? $info["placeholder_ids"] : array();
        $db->query("
            DELETE FROM {PREFIX}module_form_builder_form_placeholders
            WHERE published_form_id = :published_form_id
        ");
        $db->bind("published_form_id", $published_form_id);
        $db->execute();

        foreach ($placeholder_ids as $placeholder_id) {

            // for checkbox groups and multi-selects that don't have any selections, there won't be any values here
            if (!isset($info["placeholder_{$placeholder_id}"])) {
                continue;
            }

            // again, for checkbox groups and multi-select fields
            if (is_array($info["placeholder_{$placeholder_id}"])) {
                $values = implode("|", $info["placeholder_{$placeholder_id}"]);
            } else {
                $values = $info["placeholder_{$placeholder_id}"];
            }

            $db->query("
                INSERT INTO {PREFIX}module_form_builder_form_placeholders (published_form_id, placeholder_id, placeholder_value)
                VALUES (:published_form_id, :placeholder_id, :placeholder_values)
            ");
            $db->bindAll(array(
                "published_form_id" => $published_form_id,
                "placeholder_id" => $placeholder_id,
                "placeholder_values" => $values
            ));
            $db->execute();
        }

        return array(
            "success"           => 1,
            "published_form_id" => $published_form_id,
            "message"           => ""
        );
    }


    public static function getGeneratedFormContent($published_form_id, $filename)
    {
        $root_dir = Core::getRootDir();

        $content = <<< END
<?php

/**
 * This file was created by the Form Tools Form Builder module.
 */
require_once('$root_dir/global/library.php');
use FormTools\Core;
Core::init(array("auto_logout" => false));
\$root_dir = Core::getRootDir();
\$published_form_id = $published_form_id;
\$filename  = "$filename";
require_once("\$root_dir/modules/form_builder/form.php");
END;

        return $content;
    }
}
