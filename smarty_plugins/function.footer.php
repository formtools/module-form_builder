<?php

/**
 * For used in actually generating the form based on a template set.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_footer($params, &$smarty)
{
    $template_info = $smarty->getTemplateVars("templates");
    $smarty->assign("eval_str", $template_info["footer"]["content"]);
    return $smarty->fetch("../../modules/form_builder/smarty_plugins/eval.tpl");
}
