<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\General as CoreGeneral;
use PDO, PDOException;


class Resources
{
    public static function addResource($set_id, $resource_type, $resource_name, $placeholder, $content, $last_updated, $order)
    {
        $db = Core::$db;

        $db->query("
            INSERT INTO {PREFIX}module_form_builder_template_set_resources (resource_type, template_set_id,
              resource_name, placeholder, content, last_updated, list_order)
            VALUES (:resource_type, :set_id, :resource_name, :placeholder, :content, :last_updated, :list_order)
        ");
        $db->bindAll(array(
            "resource_type" => $resource_type,
            "set_id" => $set_id,
            "resource_name" => $resource_name,
            "placeholder" => $placeholder,
            "content" => $content,
            "last_updated" => $last_updated,
            "list_order" => $order
        ));
        $db->execute();

        return $db->getInsertId();
    }


    public static function addNewResource($set_id, $resource_name, $placeholder, $resource_type)
    {
        $list_order = self::getNextResourceListOrder($set_id);
        $now = CoreGeneral::getCurrentDatetime();

        $id = self::addResource($set_id, $resource_type, $resource_name, $placeholder, '', $now, $list_order);

        return array(
            "success" => 1,
            "message" => $id
        );
    }


    public static function getResources($set_id)
    {
        $db = Core::$db;
        
        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_set_resources
            WHERE  template_set_id = :set_id
            ORDER BY list_order
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetchAll();
    }


    public static function getResource($resource_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT *
            FROM   {PREFIX}module_form_builder_template_set_resources
            WHERE  resource_id = :resource_id
        ");
        $db->bind("resource_id", $resource_id);
        $db->execute();

        return $db->fetch();
    }


    public static function updateResource($resource_id, $info, $L)
    {
        $db = Core::$db;

        try {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_set_resources
                SET    resource_name = :resource_name,
                       resource_type = :resource_type,
                       placeholder = :placeholder,
                       content = :content,
                       last_updated = :last_updated
                WHERE  resource_id = :resource_id
            ");
            $db->bindAll(array(
                "resource_name" => $info["resource_name"],
                "resource_type" => $info["resource_type"],
                "placeholder" => $info["placeholder"],
                "content" => $info["resource_content"],
                "last_updated" => CoreGeneral::getCurrentDatetime(),
                "resource_id" => $resource_id
            ));
            $db->execute();
        } catch (PDOException $e) {
            return array(true, $L["notify_resource_not_updated"] . $e->getMessage());
        }

        return array(true, $L["notify_resource_updated"]);
    }


    public static function deleteResource($resource_id, $L)
    {
        $db = Core::$db;

        if (empty($resource_id) || !is_numeric($resource_id)) {
            return array(false, $L["notify_delete_invalid_resource_id"]);
        }

        $db->query("
            DELETE FROM {PREFIX}module_form_builder_template_set_resources
            WHERE resource_id = :resource_id
        ");
        $db->bind("resource_id", $resource_id);
        $db->execute();

        if ($db->numRows() > 0) {
            return array(true, $L["notify_resource_deleted"]);
        } else {
            return array(true, $L["notify_resource_not_deleted"]);
        }
    }


    /**
     * Figures out the next available list order for a new template set resource.
     *
     * @param integer $set_id
     */
    public static function getNextResourceListOrder($set_id)
    {
        $db = Core::$db;

        $db->query("
            SELECT list_order
            FROM   {PREFIX}module_form_builder_template_set_resources
            WHERE  template_set_id = :set_id
            ORDER BY list_order DESC
            LIMIT 1
        ");
        $db->bind("set_id", $set_id);
        $db->execute();

        return $db->fetch(PDO::FETCH_COLUMN) + 1;
    }


    public static function updateResourceOrder($info, $L)
    {
        $db = Core::$db;

        $sortable_id = $info["sortable_id"];

        $ordered_resource_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

        $order = 1;
        foreach ($ordered_resource_ids as $resource_id) {
            $db->query("
                UPDATE {PREFIX}module_form_builder_template_set_resources
                SET    list_order = $order
                WHERE  resource_id = $resource_id
            ");
            $db->bindAll(array(
                "list_order" => $order,
                "resource_id" => $resource_id
            ));
            $db->execute();
            $order++;
        }

        return array(true, $L["notify_resource_order_updated"]);
    }
}

