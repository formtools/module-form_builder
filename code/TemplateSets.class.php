<?php


namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\General as CoreGeneral;
use PDO, PDOException, Exception;


class TemplateSets
{
    private static $templateTypes = array(
        "Page Types" => array(
            "form_page"         => "phrase_form_page",
            "review_page"       => "phrase_review_page",
            "thankyou_page"     => "phrase_thankyou_page",
            "form_offline_page" => "phrase_form_offline_page"
        ),
        "Page Elements" => array(
            "header"         => "word_header",
            "footer"         => "word_footer",
            "navigation"     => "word_navigation",
            "continue_block" => "phrase_continue_block",
            "error_message"  => "phrase_error_message"
        ),
        "Other" => array(
            "page_layout"   => "phrase_page_layout",
            "code_block"    => "phrase_code_block"
        )
    );

    public static function getTemplateTypes()
    {
        return self::$templateTypes;
    }

    public static function getTemplateSets($only_return_complete = true)
    {
        $db = Core::$db;

        $is_complete_clause = ($only_return_complete) ? "AND is_complete = 'yes'" : "";
        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_sets
            WHERE  1=1
                   $is_complete_clause
            ORDER BY list_order ASC
        ");
        $db->execute();
        $rows = $db->fetchAll();

        $results = array();
        foreach ($rows as $row) {
            $set_id = $row["set_id"];

            $row["templates"]    = Templates::getTemplates($set_id);
            $row["resources"]    = Resources::getResources($set_id);
            $row["placeholders"] = Placeholders::getPlaceholders($set_id);
            $results[] = $row;
        }

        return $results;
    }


    /**
     * This returns the set ID of the first complete template set, as determined by the ordering on the
     * Template Sets page in the module.
     *
     * return integer the set ID, or the empty string if there's no complete template set
     */
    public static function getFirstTemplateSetId()
    {
        $db = Core::$db;

        $db->query("
            SELECT set_id
            FROM   {PREFIX}module_form_builder_template_sets
            WHERE  is_complete = 'yes'
            ORDER BY list_order ASC
            LIMIT 1
        ");
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN);
    }


    public static function getTemplateSet($set_id, $options = array())
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_sets
            WHERE  set_id = :set_id
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        $template_set_info = $db->fetch();
        $template_set_info["templates"]    = Templates::getTemplates($set_id, $options);
        $template_set_info["resources"]    = Resources::getResources($set_id);
        $template_set_info["placeholders"] = Placeholders::getPlaceholders($set_id);

        return $template_set_info;
    }


    /**
     * Deletes an entire template set and all associated resources, placeholders and templates. This
     * relies on the interface preventing the user from deleting
     *
     * @param integer $set_id
     */
    public static function deleteTemplateSet($set_id, $L)
    {
        $db = Core::$db;

        if (empty($set_id) || !is_numeric($set_id)) {
            return array(false, $L["notify_template_set_not_deleted"]);
        }

        // remove all the placeholders. This is done through a separate function, since the placeholder options need
        // deleting as well, which takes more work to identify the records
        $placeholders = Placeholders::getPlaceholders($set_id);
        foreach ($placeholders as $placeholder_info) {
            Placeholders::deletePlaceholder($placeholder_info["placeholder_id"], $L);
        }

        $db->query("DELETE FROM {PREFIX}module_form_builder_template_sets WHERE set_id = :set_id");
        $db->bind("set_id", $set_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_form_builder_templates WHERE set_id = :set_id");
        $db->bind("set_id", $set_id);
        $db->execute();

        $db->query("DELETE FROM {PREFIX}module_form_builder_template_set_resources WHERE template_set_id = :set_id");
        $db->bind("set_id", $set_id);
        $db->execute();

        return array(true, $L["notify_template_set_deleted"]);
    }


    /**
     * Creates a new template set.
     *
     * @param string $template_set_name
     * @param integer
     */
    public static function createNewTemplateSet($new_template_set_name, $original_set_id)
    {
        $list_order = TemplateSets::getNewTemplateSetOrder();

        if (empty($original_set_id)) {
            $set_id = TemplateSets::addTemplateSet($new_template_set_name, "1.0", "", "no", $list_order);

            $response = array(
                "success" => 1,
                "message" => $set_id
            );

        } else {
            $template_set = TemplateSets::getTemplateSet($original_set_id);

            $set_id = TemplateSets::addTemplateSet($new_template_set_name, $template_set["version"], $template_set["description"],
                $template_set["is_complete"], $list_order);

            // copy over the templates
            foreach ($template_set["templates"] as $template_info) {
                Templates::addTemplate($set_id, $template_info["template_type"], $template_info["template_name"],
                    $template_info["content"], $template_info["list_order"]);
            }

            // copy over the resources
            $now = CoreGeneral::getCurrentDatetime();
            $resources = Resources::getResources($original_set_id);
            foreach ($resources as $resource_info) {
                Resources::addResource($set_id, $resource_info["resource_type"], $resource_info["resource_name"],
                    $resource_info["placeholder"], $resource_info["content"], $now, $resource_info["list_order"]);
            }

            // copy over the placeholders
            $placeholders = Placeholders::getPlaceholders($original_set_id);

            foreach ($placeholders as $placeholder_info) {
                Placeholders::addPlaceholder($set_id, $placeholder_info["placeholder_label"],
                    $placeholder_info["placeholder"], $placeholder_info["field_type"], $placeholder_info["field_orientation"],
                    $placeholder_info["default_value"], $placeholder_info["options"]);
            }

            $response = array(
                "success" => 1,
                "message" => $set_id
            );
        }

        return $response;
    }


    /**
     * Called on the Info tab, this updates the name and description. It also checks to see whether all required
     * templates have been entered + assigns the "is_complete" value appropriately.
     */
    public static function updateTemplateSetInfo($set_id, $set_name, $desc, $version, $L)
    {
        $db = Core::$db;

        $missing_templates = self::getMissingTemplateSetTemplates($set_id);

        try {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_sets
                SET    set_name = :set_name,
                       description = :description,
                       version = :version,
                       is_complete = :is_complete
                WHERE  set_id = :set_id
            ");
            $db->bindAll(array(
                "set_name" => $set_name,
                "description" => $desc,
                "version" => $version,
                "is_complete" => empty($missing_templates) ? "yes" : "no",
                "set_id" => $set_id
            ));
            $db->execute();
        } catch (PDOException $e) {
            return array(true, $L["notify_template_set_not_updated"] . $e->getMessage());
        }

        return array(true, $L["notify_template_set_updated"]);
    }


    /**
     * Simple helper function to determine whether or not a template set is complete. This checks to confirm
     * that the template set has defined each of the templates.
     *
     * @param integer $set_id
     */
    public static function getMissingTemplateSetTemplates($set_id)
    {
        $required_templates = array(
            "form_page", "review_page", "thankyou_page", "form_offline_page", "header",
            "footer", "navigation", "continue_block", "page_layout", "error_message"
        );

        $template_set = self::getTemplateSet($set_id);

        $defined_templates = array_column($template_set["templates"], "template_type");

        return array_diff($required_templates, $defined_templates);
    }


    public static function getTemplateTypeName($template_type, $L)
    {
        $templateTypes = self::getTemplateTypes();

        $name = "";
        while (list($group_name, $types) = each($templateTypes))  {
            while (list($key, $lang_key) = each($types)) {
                if ($key == $template_type) {
                    $name = CoreGeneral::evalSmartyString("{\$" . $lang_key . "}", $L);
                }
            }
        }

        return $name;
    }


    /**
     * Returns a list of forms that use a template set plus their URLs.
     *
     * @param integer $set_id
     * @return array
     */
    public static function getTemplateSetUsage($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_forms fb, {PREFIX}forms f, {PREFIX}views v
            WHERE  fb.set_id = :set_id AND
                   fb.form_id = f.form_id AND
                   fb.view_id = v.view_id
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        $results = array();
        foreach ($db->fetchAll() as $row) {
            $form_id = $row["form_id"];
            if (!array_key_exists($form_id, $results)) {
                $results[$form_id] = array(
                    "form_name" => $row["form_name"],
                    "usage"     => array()
                );
            }

            $results[$form_id]["usage"][] = array(
                "filename"    => $row["filename"],
                "folder_url"  => $row["folder_url"],
                "folder_path" => $row["folder_path"],
                "full_url"    => $row["folder_url"] . "/" . $row["filename"],
                "view_id"     => $row["view_id"],
                "view_name"   => $row["view_name"]
            );
        }

        return $results;
    }


    /**
     * Called on the main templates page in the module, when the user manually re-sorted the template sets.
     */
    public static function updateTemplateSetOrder($info, $L)
    {
        $db = Core::$db;

        $sortable_id   = $info["sortable_id"];
        $sortable_rows = explode(",", $info["{$sortable_id}_sortable__rows"]);

        $order = 1;
        foreach ($sortable_rows as $set_id) {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_sets
                SET    list_order = :list_order
                WHERE  set_id = :set_id
            ");
            $db->bindAll(array(
                "list_order" => $order,
                "set_id" => $set_id
            ));
            $db->execute();
            $order++;
        }

        return array(true, $L["notify_template_set_order_updated"]);
    }


    /**
     * Used in the Form Builder to provide a default template set + templates when the page first loads.
     *
     * @return mixed
     */
    public static function getDefaultTemplateSet()
    {
        // first, check the there's at least one complete template set available. If not, we're not
        // going to get very far
        $set_id = self::getFirstTemplateSetId();
        if (empty($set_id)) {
            return "";
        }

        return array(
            "set_id"    => $set_id,
            "templates" => self::getTemplates($set_id)
        );
    }


    /**
     * Returns an ordered list of template IDs in a template set.
     *
     * @param integer $set_id
     */
    public static function getTemplateIds($set_id)
    {
        $templates = Templates::getTemplates($set_id);
        return array_column($templates, "template_id");
    }


    public static function getTemplateSetPrevNextLinks($set_id)
    {
        $template_sets = self::getTemplateSets(false);

        $sorted_set_ids = array_column($template_sets, "set_id");
        $current_index = array_search($set_id, $sorted_set_ids);

        $return_info = array(
            "prev_set_id" => "",
            "next_set_id" => ""
        );

        if ($current_index === 0) {
            if (count($sorted_set_ids) > 1) {
                $return_info["next_set_id"] = $sorted_set_ids[$current_index + 1];
            }
        } else if ($current_index === count($sorted_set_ids)-1) {
            if (count($sorted_set_ids) > 1) {
                $return_info["prev_set_id"] = $sorted_set_ids[$current_index - 1];
            }
        } else {
            $return_info["prev_set_id"] = $sorted_set_ids[$current_index-1];
            $return_info["next_set_id"] = $sorted_set_ids[$current_index+1];
        }

        return $return_info;
    }


    /**
     * This generates both the main default_sets.php file (found in this folder) used for fresh installations,
     * and for generating a custom template set file for import/export.
     */
    public static function createDefaultTemplateSetFile($set_id = "")
    {
        $php_lines = array();

        $template_sets = array();
        if (empty($set_id)) {
            $template_sets = self::getTemplateSets(false);
        } else {
            $template_sets[] = self::getTemplateSet($set_id);
        }

        $set_order = 1;
        foreach ($template_sets as $template_set_info) {
            $php_lines[] = "\$g_default_sets[] = array(";

            $set_name = $template_set_info["set_name"];
            $version = $template_set_info["version"];
            $description = addcslashes($template_set_info["description"], '"');

            // escape values
            $php_lines[] = "  \"set_name\"    => \"{$set_name}\",";
            $php_lines[] = "  \"version\"     => \"{$version}\",";
            $php_lines[] = "  \"description\" => \"{$description}\",";
            $php_lines[] = "  \"is_complete\" => \"{$template_set_info["is_complete"]}\",";
            $php_lines[] = "  \"list_order\"  => $set_order,\n";
            $php_lines[] = "  // templates";

            // templates
            if (empty($template_set_info["templates"])) {
                $php_lines[] = "  \"templates\" => array(),\n";
            } else {
                $php_lines[] = "  \"templates\" => array(";
                $template_lines = array();
                foreach ($template_set_info["templates"] as $template_info) {
                    $content = preg_replace("/\r?\n/", "\\n", $template_info["content"]);
                    $content = preg_replace("/\"/", "\\\"", $content);
                    $content = preg_replace('/\$/', '\\\$', $content);
                    $template_lines[] = <<< END
    array(
      "template_type" => "{$template_info["template_type"]}",
      "template_name" => "{$template_info["template_name"]}",
      "content"       => "{$content}"
    )
END;
                }
                $php_lines[] = implode(",\n", $template_lines);
                $php_lines[] = "  ),\n";
            }

            $php_lines[] = "  // resources";

            // resources
            if (empty($template_set_info["resources"])) {
                $php_lines[] = "  \"resources\" => array(),\n";
            } else {
                $php_lines[] = "  \"resources\" => array(";
                $resource_lines = array();
                foreach ($template_set_info["resources"] as $resource_info) {
                    $content = preg_replace("/\r\n/", "\\n", $resource_info["content"]);
                    $content = preg_replace("/\"/", "\\\"", $content);
                    $content = preg_replace('/\$/', '\\\$', $content);
                    $resource_lines[] =<<< END
    array(
      "resource_type" => "{$resource_info["resource_type"]}",
      "resource_name" => "{$resource_info["resource_name"]}",
      "placeholder"   => "{$resource_info["placeholder"]}",
      "content"       => "{$content}",
      "last_updated"  => "{$resource_info["last_updated"]}"
    )
END;
                }
                $php_lines[] = implode(",\n", $resource_lines);
                $php_lines[] = "  ),\n";
            }

            $php_lines[] = "  // placeholders";

            // placeholders
            if (empty($template_set_info["placeholders"])) {
                $php_lines[] = "  \"placeholders\" => array()";
            } else {
                $php_lines[] = "  \"placeholders\" => array(";
                $resource_lines = array();
                foreach ($template_set_info["placeholders"] as $placeholder_info) {
                    $str =<<< END
    array(
      "placeholder_label" => "{$placeholder_info["placeholder_label"]}",
      "placeholder"       => "{$placeholder_info["placeholder"]}",
      "field_type"        => "{$placeholder_info["field_type"]}",
      "field_orientation" => "{$placeholder_info["field_orientation"]}",
      "default_value"     => "{$placeholder_info["default_value"]}",

END;

                    // placeholder options
                    if (empty($placeholder_info["options"])) {
                        $str .= "      \"options\" => array()\n";
                    } else {
                        $str .= "      \"options\" => array(\n";

                        $option_lines = array();
                        foreach ($placeholder_info["options"] as $option_info) {
                            $option_text = preg_replace("/\"/", "\\\"", $option_info["option_text"]);
                            $option_lines[] = "        array(\"option_text\" => \"$option_text\")";
                        }

                        $str .= implode(",\n", $option_lines) . "\n      )\n";
                    }

                    $str .= "    )";

                    $resource_lines[] = $str;
                }
                $php_lines[] = implode(",\n", $resource_lines);
                $php_lines[] = "  )";
            }

            $php_lines[] = ");\n";
            $set_order++;
        }

        return implode("\n", $php_lines);
    }


    public static function importTemplateSet($filename, $L)
    {
        $root_dir = Core::getRootDir();

        if (!is_file("$root_dir/modules/form_builder/share/$filename")) {
            return array(false, $L["notify_invalid_template_set_filename"]);
        }

        require_once("$root_dir/modules/form_builder/share/$filename");
        self::importTemplateSetData($g_default_sets);

        return array(true, $L["notify_template_set_imported"]);
    }


    /**
     * Helper function to determine whether or not a Template Set actually exists or not.
     *
     * @param integer $set_id
     */
    public static function checkTemplateSetExists($set_id)
    {
        $db = Core::$db;

        if (empty($set_id) || !is_numeric($set_id)) {
            return false;
        }

        $db->query("
            SELECT count(*)
            FROM   {PREFIX}module_form_builder_template_sets
            WHERE  set_id = :set_id
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) == 1;
    }

    /**
     * Generates an export file for a Template Set.
     *
     * @param integer $set_id
     */
    public static function generateTemplateSetExportFile($set_id)
    {
        if (!self::checkTemplateSetExists($set_id)) {
            echo "Invalid Template Set ID";
            exit;
        }

        $php = self::createDefaultTemplateSetFile($set_id);

        return "<textarea style=\"width: 100%; height: 100%\"><?php\n\n$php</textarea>";
    }


    public static function importTemplateSetData($template_set)
    {
        $template_set_order = self::getNewTemplateSetOrder();

        try {
            // insert the new template set
            $set_id = self::addTemplateSet($template_set->template_set_name, $template_set->template_set_version,
                $template_set->description, "yes", $template_set_order);

            // templates
            $template_order = 1;
            foreach ($template_set->templates as $template_type => $templates) {
                foreach ($templates as $template_info) {
                    Templates::addTemplate($set_id, $template_type, $template_info->template_name, $template_info->content, $template_order);
                    $template_order++;
                }
            }

            $now = CoreGeneral::getCurrentDatetime();

            // resources
            $resource_order = 1;
            if (isset($template_set->resources->css)) {
                foreach ($template_set->resources->css as $resource_info) {
                    Resources::addResource($set_id, "css", $resource_info->resource_name, $resource_info->placeholder,
                        $resource_info->content, $now, $resource_order);
                    $resource_order++;
                }
            }
            if (isset($template_set->resources->js)) {
                foreach ($template_set->resources->js as $resource_info) {
                    Resources::addResource($set_id, "js", $resource_info->resource_name, $resource_info->placeholder,
                       $resource_info->content, $now, $resource_order);
                    $resource_order++;
                }
            }

            // placeholders
            if (isset($template_set->placeholders)) {
                foreach ($template_set->placeholders as $placeholder_info) {
                    $options = isset($placeholder_info->options) ? $placeholder_info->options : array();

                    // convert the $options into a simple array
                    $options_hash = array();
                    foreach ($options as $option) {
                        $options_hash[] = array(
                            "option_text" => $option->option_text
                        );
                    }

                    Placeholders::addPlaceholder($set_id, $placeholder_info->placeholder_label,
                        $placeholder_info->placeholder, $placeholder_info->field_type, $placeholder_info->field_orientation,
                        $placeholder_info->default_value, $options_hash);
                }
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }


    // -----------------------------------------------------------------------------------------------------------------


    private static function addTemplateSet($set_name, $version, $description, $is_complete, $list_order)
    {
        $db = Core::$db;

        $db->query("
            INSERT INTO {PREFIX}module_form_builder_template_sets (set_name, version, description, is_complete, list_order)
            VALUES (:set_name, :version, :description, :is_complete, :list_order)
        ");
        $db->bindAll(array(
            "set_name" => $set_name,
            "version" => $version,
            "description" => $description,
            "is_complete" => $is_complete,
            "list_order" => $list_order
        ));
        $db->execute();

        return $db->getInsertId();
    }


    /**
     * Returns the last number + 1 for new template set creation.
     *
     * @return integer
     */
    private static function getNewTemplateSetOrder()
    {
        $db = Core::$db;

        $db->query("
            SELECT list_order
            FROM   {PREFIX}module_form_builder_template_sets
            ORDER BY list_order DESC
            LIMIT 1
        ");
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) + 1;
    }
}

