<?php

/**
 * Includes a code block.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_code_block($params, &$smarty)
{
	if (!isset($params["template_id"]) || !is_numeric($params["template_id"]))
	{
		echo "The template ID attribute is missing on the code block include.";
		exit;
	}

  $template_info = fb_get_template($params["template_id"]);
  if (empty($template_info) || $template_info["template_type"] != "code_block")
  {
  	echo "The template ID being passed to the code block is invalid.";
		exit;
  }

  $smarty->assign("eval_str", $template_info["content"]);
  return $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");
}
