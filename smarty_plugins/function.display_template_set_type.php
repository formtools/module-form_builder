<?php

use FormTools\Modules\FormBuilder\TemplateSets;


// yeesh... terminology for "sections" / "types" / "names" is terrible... they all refer to the same thing
function smarty_function_display_template_set_type($params, &$smarty)
{
    $template_types = TemplateSets::getTemplateTypes();

    $type = $params["type"];

    foreach ($template_types as $group_name => $vals) {
        $found = false;
        foreach ($vals as $section_key => $section_name) {
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
