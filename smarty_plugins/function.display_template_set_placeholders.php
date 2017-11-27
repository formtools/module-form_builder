<?php

use FormTools\Modules;
use FormTools\Modules\FormBuilder\Placeholders;

function smarty_function_display_template_set_placeholders($params, &$smarty)
{
    $module = Modules::getModuleInstance("form_builder");

    $L = $module->getLangStrings();
    echo Placeholders::generateTemplateSetPlaceholdersHtml($params["placeholders"], $params["placeholder_hash"], $L);
}

