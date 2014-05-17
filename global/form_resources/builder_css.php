<?php

/**
 * This is a version of the css.php that's designed for the form builder. The difference is that this
 * is passed the placeholder info directly from the builder, not pulling it from the database.
 */

header("Content-Type: text/css");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

$g_check_ft_sessions = false;
require_once(realpath(dirname(__FILE__) . "/../../../../global/library.php"));
ft_include_module("form_builder");

$resource_id = $_GET["resource_id"];
//$published_form_id = $_GET["id"];

$resource_info = fb_get_resource($resource_id);
$set_id = $resource_info["template_set_id"];
$css    = $resource_info["content"];

//$config = fb_get_form_configuration($published_form_id);
$config = array();
$smarty = fb_create_new_smarty_instance();

/*
$placeholders = fb_get_placeholders($set_id);
$placeholder_hash = array();
foreach ($placeholders as $placeholder_info)
{
	$placeholder_id = $placeholder_info["placeholder_id"];
	$placeholder    = $placeholder_info["placeholder"];
  $placeholder_hash[$placeholder_id] = $placeholder;
}

foreach ($config["placeholders"] as $placeholder_info)
{
  $curr_placeholder_id = $placeholder_info["placeholder_id"];
  $val = $placeholder_info["placeholder_value"];

  if (!isset($placeholder_hash[$curr_placeholder_id]))
    continue;

  $placeholder = $placeholder_hash[$curr_placeholder_id];
  $smarty->assign($placeholder, $val);
}
*/

$smarty->assign("eval_str", $css);
echo $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");
