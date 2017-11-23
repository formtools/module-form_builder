<?php

use FormTools\Modules\FormBuilder\Placeholders;

function smarty_function_display_template_set_placeholders($params, &$smarty)
{
    echo Placeholders::generateTemplateSetPlaceholdersHtml($params["set_id"], $params["placeholders"],
        $params["placeholder_hash"]);
}

