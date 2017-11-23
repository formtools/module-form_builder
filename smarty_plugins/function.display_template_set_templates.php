<?php

use FormTools\Modules\FormBuilder\Templates;

function smarty_function_display_template_set_templates($params, &$smarty)
{
    echo Templates::generateTemplateSetTemplatesHtml($params["set_id"], $params["selected_templates"]);
}
