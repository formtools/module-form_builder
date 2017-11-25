<?php

use FormTools\Modules;
use FormTools\Modules\FormBuilder\Templates;

function smarty_function_display_template_set_templates($params, &$smarty)
{
    $module = Modules::getModuleInstance("form_builder");
    $L = $module->getLangStrings();
    echo Templates::generateTemplateSetTemplatesHtml($params["set_id"], $L, $params["selected_templates"]);
}
