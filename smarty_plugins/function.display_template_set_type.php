<?php

use FormTools\Modules\FormBuilder\TemplateSets;


// yeesh... terminology for "sections" / "types" / "names" is terrible... they all refer to the same thing
function smarty_function_display_template_set_type($params, &$smarty)
{
    $template_types = TemplateSets::getTemplateTypes();

    $type = $params["type"];

    while (list($group_name, $vals) = each($template_types)) {
        $found = false;
        while (list($section_key, $section_name) = each($vals)) {
            if ($section_key == $type) {
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
