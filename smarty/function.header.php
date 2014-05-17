<?php

/**
 * For used in actually generating the form based on a template set.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_header($params, &$smarty)
{
  $template_info = $smarty->_tpl_vars["templates"]["header"];
  $smarty->assign("eval_str", $template_info["content"]);
  return $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");
}
