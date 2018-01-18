<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use PDO;


class Placeholders
{

    public static function addPlaceholder($set_id, $placeholder_label, $placeholder, $field_type, $field_orientation,
        $default_value, $placeholder_options = array())
    {
        $db = Core::$db;

        $next_order = self::getNextPlaceholderOrder($set_id);

        // add the main record first
        $db->query("
            INSERT INTO {PREFIX}module_form_builder_template_set_placeholders (set_id, placeholder_label, placeholder,
                field_type, field_orientation, default_value, field_order)
            VALUES (:set_id, :placeholder_label, :placeholder, :field_type, :field_orientation, :default_value, :field_order)
        ");
        $db->bindAll(array(
            "set_id" => $set_id,
            "placeholder_label" => $placeholder_label,
            "placeholder" => $placeholder,
            "field_type" => $field_type,
            "field_orientation" => $field_orientation,
            "default_value" => $default_value,
            "field_order" => $next_order
        ));
        $db->execute();
        $placeholder_id = $db->getInsertId();

        // if this field had multiple options, add them too
        if (in_array($field_type, array("select", "multi-select", "radios", "checkboxes")) && !empty($placeholder_options)) {
            $field_order = 1;

            foreach ($placeholder_options as $row) {
                if (empty($row["option_text"])) {
                    continue;
                }

                $db->query("
                    INSERT INTO {PREFIX}module_form_builder_template_set_placeholder_opts (placeholder_id, option_text, field_order)
                    VALUES (:placeholder_id, :option_text, :field_order)
                ");
                $db->bindAll(array(
                    "placeholder_id" => $placeholder_id,
                    "option_text" => $row["option_text"],
                    "field_order" => $field_order
                ));
                $db->execute();

                $field_order++;
            }
        }

        return array(true, "");
    }


    /**
     * Simple delete function.
     *
     * @param integer $placeholder_id
     */
    public static function deletePlaceholder($placeholder_id, $L)
    {
        $db = Core::$db;

        $placeholder_info = Placeholders::getPlaceholder($placeholder_id);

        if (empty($placeholder_id) || !is_numeric($placeholder_id)) {
            return array(false, $L["notify_delete_invalid_placeholder_id"]);
        }

        $db->query("
            DELETE FROM {PREFIX}module_form_builder_template_set_placeholders
            WHERE placeholder_id = :placeholder_id
        ");
        $db->bind("placeholder_id", $placeholder_id);
        $db->execute();

        if ($db->numRows() > 0) {
            $db->query("
                DELETE FROM {PREFIX}module_form_builder_template_set_placeholder_opts
                WHERE placeholder_id = :placeholder_id
            ");
            $db->bind("placeholder_id", $placeholder_id);
            $db->execute();

            if (!empty($placeholder_info) && isset($placeholder_info["set_id"])) {
                Placeholders::updatePlaceholderOrder($placeholder_info["set_id"]);
            }
            return array(true, $L["notify_placeholder_deleted"]);
        }

        return array(true, $L["notify_placeholder_not_deleted"]);
    }


    /**
     * Called on the Template Set -> Edit Placeholder page.
     *
     * @param integer $set_id
     * @param array $info
     */
    public static function updatePlaceholder($placeholder_id, $info, $L)
    {
        $db = Core::$db;
        $field_type = $info["field_type"];

        // add the main record first
        $db->query("
            UPDATE {PREFIX}module_form_builder_template_set_placeholders
            SET    placeholder_label = :placeholder_label,
                   placeholder = :placeholder,
                   field_type = :field_type,
                   field_orientation = :field_orientation,
                   default_value = :default_value
            WHERE  placeholder_id = :placeholder_id
        ");
        $db->bindAll(array(
            "placeholder_label" => $info["placeholder_label"],
            "placeholder" => $info["placeholder"],
            "field_type" => $field_type,
            "field_orientation" => $info["field_orientation"],
            "default_value" => $info["default_value"],
            "placeholder_id" => $placeholder_id
        ));
        $db->execute();

        // if this field had multiple options, add them too
        $placeholder_options = isset($info["placeholder_options"]) ? $info["placeholder_options"] : array();
        $db->query("
            DELETE FROM {PREFIX}module_form_builder_template_set_placeholder_opts
            WHERE placeholder_id = $placeholder_id
        ");
        $db->bind("placeholder_id", $placeholder_id);
        $db->execute();

        if (in_array($field_type, array("select", "multi-select", "radios", "checkboxes")) && !empty($placeholder_options)) {
            $field_order = 1;

            foreach ($placeholder_options as $option) {
                if (empty($option)) {
                    continue;
                }

                $db->query("
                    INSERT INTO {PREFIX}module_form_builder_template_set_placeholder_opts (placeholder_id, option_text, field_order)
                    VALUES (:placeholder_id, :option_text, :field_order)
                ");
                $db->bindAll(array(
                    "placeholder_id" => $placeholder_id,
                    "option_text" => $option,
                    "field_order" => $field_order
                ));
                $db->execute();

                $field_order++;
            }
        }

        return array(true, $L["notify_placeholder_updated"]);
    }


    public static function getPlaceholder($placeholder_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_set_placeholders
            WHERE  placeholder_id = :placeholder_id
        ");
        $db->bind("placeholder_id", $placeholder_id);
        $db->execute();

        $result = $db->fetch();

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_set_placeholder_opts
            WHERE  placeholder_id = :placeholder_id
            ORDER BY field_order
        ");
        $db->bind("placeholder_id", $placeholder_id);
        $db->execute();

        $result["options"] = $db->fetchAll();

        return $result;
    }


    // -----------------------------------------------------------------------------------------------------------------


    private static function getNextPlaceholderOrder($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT field_order
            FROM   {PREFIX}module_form_builder_template_set_placeholders
            WHERE  set_id = :set_id
            ORDER BY field_order DESC
            LIMIT 1
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) + 1;
    }


    /**
     * Returns all placeholders for a template set.
     *
     * @param integer $set_id
     */
    public static function getPlaceholders($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_set_placeholders
            WHERE  set_id = :set_id
            ORDER BY field_order
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        $rows = $db->fetchAll();
        $results = array();
        foreach ($rows as $row) {
            $db->query("
                SELECT *
                FROM   {PREFIX}module_form_builder_template_set_placeholder_opts
                WHERE  placeholder_id = :placeholder_id
                ORDER BY field_order
            ");
            $db->bind("placeholder_id", $row["placeholder_id"]);
            $db->execute();

            $row["options"] = $db->fetchAll();
            $results[] = $row;
        }

        return $results;
    }



    /**
     * Called on the main Placeholders page - it deletes unwanted placeholders and re-orders those the
     * user wants to keep.
     *
     * @param array $info
     */
    public static function updatePlaceholders($info, $L)
    {
        $db = Core::$db;

        $sortable_id = $info["sortable_id"];

        // delete any unwanted placeholders
        $deleted_placeholder_ids_str = $info["{$sortable_id}_sortable__deleted_rows"];
        if (!empty($deleted_placeholder_ids_str)) {
            $db->query("
                DELETE FROM {PREFIX}module_form_builder_template_set_placeholders
                WHERE placeholder_id IN ($deleted_placeholder_ids_str)
            ");
            $db->execute();

            $db->query("
                DELETE FROM {PREFIX}module_form_builder_template_set_placeholder_opts
                WHERE placeholder_id IN ($deleted_placeholder_ids_str)
            ");
            $db->execute();
        }

        $placeholder_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

        $order = 1;
        foreach ($placeholder_ids as $placeholder_id) {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_set_placeholders
                SET    field_order = :field_order
                WHERE  placeholder_id = :placeholder_id
            ");
            $db->bindAll(array(
                "field_order" => $order,
                "placeholder_id" => $placeholder_id
            ));
            $db->execute();
            $order++;
        }

        return array(true, $L["notify_placeholders_updated"]);
    }


    public static function getNumPlaceholders($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT count(*)
            FROM   {PREFIX}module_form_builder_template_set_placeholders
            WHERE  set_id = :set_id
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN);
    }


    /**
     * Called after a placeholder gets deleted.
     *
     * @param integer $set_id
     */
    public static function updatePlaceholderOrder($set_id)
    {
        $db = Core::$db;

        $placeholders = Placeholders::getPlaceholders($set_id);

        $list_order = 1;
        foreach ($placeholders as $info) {
            $placeholder_id = $info["placeholder_id"];

            $db->query("
                UPDATE {PREFIX}module_form_builder_template_set_placeholders
                SET    field_order = :field_order
                WHERE  placeholder_id = :placeholder_id
            ");
            $db->bindAll(array(
                "field_order" => $list_order,
                "placeholder_id" => $placeholder_id
            ));
            $db->execute();

            $list_order++;
        }
    }


    /**
     * Called by the Form Builder to generate the markup for the Placeholders section in the sidebar.
     *
     * @param array $placeholders
     * @param array $placeholder_hash
     */
    public static function generateTemplateSetPlaceholdersHtml($placeholders, $placeholder_hash = array(), $L)
    {
        $smarty = General::createNewSmartyInstance("single");
        $smarty->assign("placeholders", $placeholders);
        $smarty->assign("placeholder_hash", $placeholder_hash);
        $smarty->assign("L", $L);

        $html = $smarty->fetch("../../modules/form_builder/smarty_plugins/placeholders_html.tpl");

        return $html;
    }

}

