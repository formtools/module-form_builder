<?php

/**
 * For used in actually generating the form based on a template set.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_error_message($params, &$smarty)
{
  $template_info = $smarty->_tpl_vars["templates"]["error_message"];
  $smarty->assign("eval_str", $template_info["content"]);
  return $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");
}
