<?php

require_once("../../../global/library.php");

use FormTools\Modules;
use FormTools\Modules\FormBuilder\Forms;
use FormTools\Modules\FormBuilder\Placeholders;
use FormTools\Modules\FormBuilder\Resources;
use FormTools\Modules\FormBuilder\Templates;
use FormTools\Modules\FormBuilder\TemplateSets;

$module = Modules::initModulePage("admin");

$module = Modules::getModuleInstance("form_builder");
$L = $module->getLangStrings();


// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$action = $request["action"];

switch ($action) {
    case "create_new_template_set":
        $template_set_name = $request["template_set_name"];
        $original_set_id = isset($request["original_set_id"]) ? $request["original_set_id"] : "";
        $results = TemplateSets::createNewTemplateSet($template_set_name, $original_set_id);
        echo returnJSON($results);
        break;

    case "create_new_template":
        $results = Templates::createNewTemplate($request["set_id"], $request["template_name"], $request["template_type"],
            $request["new_template_source"], $request["source_template_id"]);
        echo returnJSON($results);
        break;

    case "add_resource":
        $set_id = $request["set_id"];
        $resource_name = $request["resource_name"];
        $placeholder = $request["placeholder"];
        $resource_type = $request["resource_type"];
        $result = Resources::addNewResource($set_id, $resource_name, $placeholder, $resource_type);
        echo returnJSON($result);
        break;

    case "save_builder_settings":
        $result = Forms::saveBuilderSettings($request);
        echo returnJSON($result);
        break;

    case "get_template_set_templates_html":
        $set_id = $request["set_id"];
        echo Templates::generateTemplateSetTemplatesHtml($set_id, $L);
        break;

    case "get_template_set_placeholders_html":
        $set_id = $request["set_id"];
        $placeholders = Placeholders::getPlaceholders($set_id);

        // set the default values
        $placeholder_hash = array();
        foreach ($placeholders as $p_info) {
            $placeholder_hash[$p_info["placeholder_id"]] = $p_info["default_value"];
        }
        echo Placeholders::generateTemplateSetPlaceholdersHtml($placeholders, $placeholder_hash, $L);
        break;

    case "publish_form":
        $module_settings = $module->getSettings();
        if ($module_settings["demo_mode"] != "on") {
            $result = Forms::publishForm($request, $L);
            echo returnJSON($result);
        }
        break;

    case "update_publish_settings":
        $module_settings = $module->getSettings();
        if ($module_settings["demo_mode"] != "on") {
            $result = Forms::updatePublishSettings($request, $L);
            echo returnJSON($result);
        }
        break;
}

function returnJSON($php)
{
    header("Content-Type: application/json");
    return json_encode($php);
}
