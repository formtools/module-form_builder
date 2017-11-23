<?php

use FormTools\Core;
use FormTools\General;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\TemplateSets;


function smarty_function_template_types($params, &$smarty)
{
    $LANG = Core::$L;
    $template_types = TemplateSets::getTemplateTypes();

    $module = Modules::getModuleInstance("form_builder");
    $L = $module->getLangStrings();

    if (empty($params["name_id"])) {
        $smarty->trigger_error("assign: missing 'name_id' parameter. This is used to give the select field a name and id value.");
        return;
    }
    $name_id = $params["name_id"];
    $class = isset($params["class"]) ? $params["class"] : "";
    $default_value = (isset($params["default"])) ? $params["default"] : "";

    $lines = array(
        "<select id=\"$name_id\" name=\"$name_id\" class=\"$class\">"
    );

    $lines[] = "<option value=\"\">{$LANG["phrase_please_select"]}</option>";

    while (list($group_name, $group_sections) = each($template_types)) {
        $lines[] = "<optgroup label=\"$group_name\">";
        while (list($key, $value) = each($group_sections)) {
            $template_type_label = General::evalSmartyString("{\$" . $value . "}", $L);
            $selected = ($key == $default_value) ? "selected" : "";
            $lines[] = "<option value=\"$key\" {$selected}>$template_type_label</option>";
        }
        $lines[] = "</optgroup>";
    }
    $lines[] = "</select>";

    echo implode("\n", $lines);
}

