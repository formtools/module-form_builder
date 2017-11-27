<?php




namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use PDO, PDOException;


class Templates
{

    public static function addTemplate($set_id, $template_type, $template_name, $content, $list_order)
    {
        $db = Core::$db;

        $db->query("
            INSERT INTO {PREFIX}module_form_builder_templates (set_id, template_type, template_name, content, list_order)
            VALUES (:set_id, :template_type, :template_name, :content, :list_order)
        ");
        $db->bindAll(array(
            "set_id" => $set_id,
            "template_type" => $template_type,
            "template_name" => $template_name,
            "content" => $content,
            "list_order" => $list_order
        ));
        $db->execute();

        return $db->getInsertId();
    }


    /**
     * Creates a new template.
     *
     * @param integer $set_id
     * @param array $info
     */
    public static function createNewTemplate($set_id, $template_name, $template_type, $new_template_source, $source_template_id)
    {
        $db = Core::$db;

        $list_order = Templates::getNextTemplateOrder($set_id);

        // if the template set had one or more templates already in it, the Create New Template dialog
        // would have offered the option to create the template based on an existing one.
        if ($new_template_source == "existing_template") {
            $old_template_info = Templates::getTemplate($source_template_id);

            $db->query("
                INSERT INTO {PREFIX}module_form_builder_templates (set_id, template_name, template_type,
                    content, list_order)
                VALUES (:set_id, :template_name, :template_type, :content, :list_order)
            ");
            $db->bindAll(array(
                "set_id" => $set_id,
                "template_name" => $template_name,
                "template_type" => $old_template_info["template_type"],
                "content" => $old_template_info["content"],
                "list_order" => $list_order
            ));
            $db->execute();

        } else {
            $db->query("
                INSERT INTO {PREFIX}module_form_builder_templates (set_id, template_name, template_type, list_order)
                VALUES (:set_id, :template_name, :template_type, :list_order)
            ");
            $db->bindAll(array(
                "set_id" => $set_id,
                "template_name" => $template_name,
                "template_type" => $template_type,
                "list_order" => $list_order
            ));
            $db->execute();
        }

        $success = 1;
        $message = $db->getInsertId();

        $missing_templates = TemplateSets::getMissingTemplateSetTemplates($set_id);
        if (empty($missing_templates)) {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_sets
                SET    is_complete = 'yes'
                WHERE  set_id = :set_id
            ");
            $db->bind("set_id", $set_id);
            $db->execute();
        }

        return array(
            "success" => $success,
            "message" => $message
        );
    }


    public static function getTemplate($template_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_templates
            WHERE  template_id = :template_id
        ");
        $db->bind("template_id", $template_id);
        $db->execute();

        return $db->fetch();
    }


    /**
     * Returns all templates in a template set.
     *
     * @param $set_id
     * @return array
     */
    public static function getTemplates($set_id, $options = array())
    {
        $db = Core::$db;

        $get_template_usage = isset($options["get_template_usage"]) && $options["get_template_usage"];

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_templates
            WHERE  set_id = :set_id
            ORDER BY list_order
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        $rows = $db->fetchAll();

        $templates = array();
        foreach ($rows as $row) {
            if ($get_template_usage) {
                $template_id = $row["template_id"];
                $row["usage"] = Templates::getTemplateUsage($template_id);
            }
            $templates[] = $row;
        }

        return $templates;
    }

    /**
     * Returns all templates in a template set.
     *
     * @param $set_id
     * @return array
     */
    public static function getTemplatesGroupedByType($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_templates
            WHERE  set_id = :set_id
            ORDER BY list_order
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        $grouped_templates = array();
        foreach ($db->fetchAll() as $row) {
            if (!array_key_exists($row["template_type"], $grouped_templates)) {
                $grouped_templates[$row["template_type"]] = array();
            }
            $grouped_templates[$row["template_type"]][] = $row;
        }

        return $grouped_templates;
    }


    /**
     * Updates the template content.
     */
    public static function updateTemplate($template_info, $L)
    {
        $db = Core::$db;

        try {
            $db->query("
                UPDATE {PREFIX}module_form_builder_templates
                SET    template_name = :template_name,
                       content = :content
                WHERE  template_id = :template_id
                LIMIT 1
            ");
            $db->bindAll(array(
            "template_name" => $template_info["template_name"],
            "content" => $template_info["template_content"],
            "template_id" => $template_info["template_id"]
            ));
            $db->execute();
        } catch (PDOException $e) {
            return array(false, $L["notify_template_not_updated"] . $e->getMessage());
        }

        return array(true, $L["notify_template_updated"]);
    }



    public static function deleteTemplate($template_id, $L)
    {
        $db = Core::$db;

        $template_info = Templates::getTemplate($template_id);

        $db->query("DELETE FROM {PREFIX}module_form_builder_templates WHERE template_id = :template_id");
        $db->bind("template_id", $template_id);
        $db->execute();

        if ($db->numRows() === 1) {
            $set_id = $template_info["set_id"];
            $missing_templates = TemplateSets::getMissingTemplateSetTemplates($set_id);

            if (!empty($missing_templates)) {
                $db->query("
                    UPDATE {PREFIX}module_form_builder_template_sets
                    SET    is_complete = 'no'
                    WHERE  set_id = :set_id
                ");
                $db->bind("set_id", $set_id);
                $db->execute();
            }
            return array(true, $L["notify_template_deleted"]);
        }

        return array(false, $L["notify_template_not_deleted"]);
    }


    public static function getTemplateType($set_id, $template_type)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_templates
            WHERE  set_id = :set_id AND
                   template_type = :template_type
            ORDER BY list_order
        ");
        $db->bindAll(array(
            "set_id" => $set_id,
            "template_type" => $template_type
        ));
        $db->execute();

        return $db->fetchAll();
    }


    public static function updateTemplateOrder($info, $L)
    {
        $db = Core::$db;

        $sortable_id = $info["sortable_id"];
        $template_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

        $order = 1;
        foreach ($template_ids as $template_id) {
            $db->query("
                UPDATE {PREFIX}module_form_builder_templates
                SET    list_order = :list_order
                WHERE  template_id = :template_id
            ");
            $db->bindAll(array(
                "list_order" => $order,
                "template_id" => $template_id
            ));
            $db->execute();

            $order++;
        }

        return array(true, $L["notify_template_order_updated"]);
    }


    public static function getNextTemplateOrder($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT list_order
            FROM   {PREFIX}module_form_builder_templates
            WHERE  set_id = :set_id
            ORDER BY list_order DESC
            LIMIT 1
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) + 1;
    }


    public static function generateTemplateSetTemplatesHtml($set_id, $L, $selected_templates = array())
    {
        $grouped_templates = Templates::getTemplatesGroupedByType($set_id);

        $smarty = General::createNewSmartyInstance("single");
        $smarty->assign("grouped_templates", $grouped_templates);
        $smarty->assign("selected_templates", $selected_templates);
        $smarty->assign("L", $L);

        $html = $smarty->fetch("../../modules/form_builder/smarty_plugins/templates_html.tpl");

        return $html;
    }


    /**
     * Returns a list of forms that use a template set, and all their URLs.
     *
     * @param integer $set_id
     * @return array
     */
    public static function getTemplateUsage($template_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT f.form_id, f.form_name, fb.filename, fb.folder_path, fb.folder_url
            FROM   {PREFIX}module_form_builder_form_templates t,
                   {PREFIX}module_form_builder_forms fb,
                   {PREFIX}forms f
            WHERE  t.template_id = :template_id AND
                   t.published_form_id = fb.published_form_id AND
                   fb.form_id = f.form_id
        ");
        $db->bind("template_id", $template_id);
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
            );
        }

        return $results;
    }
}
