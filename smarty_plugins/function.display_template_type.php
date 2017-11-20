<?php

use FormTools\General;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\TemplateSets;

function smarty_function_display_template_type($params, &$smarty)
{
    $module = Modules::getModuleInstance("form_builder");
    $L = $module->getLangStrings();
    $template_types = TemplateSets::getTemplateTypes();

    $type = $params["type"];

    while (list($group_name, $vals) = each($template_types)) {
        $found = false;
        while (list($section_key, $section_name) = each($vals)) {
            if ($section_key == $type) {
                $section_name = General::evalSmartyString("{\$" . $section_name . "}", $L);
                echo "<span class=\"set_type_$section_key\">$section_name</span>";
                $found = true;
                break;
            }
        }
        if ($found) {
            break;
        }
    }
}
