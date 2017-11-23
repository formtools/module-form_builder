<?php

use FormTools\Modules\FormBuilder\TemplateSets;

function smarty_function_display_template_set_name($params, &$smarty)
{
    $set_id = $params["set_id"];
    $set_info = TemplateSets::getTemplateSet($set_id);
    echo $set_info["set_name"];
}
