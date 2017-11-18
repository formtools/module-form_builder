<?php

/**
 * This is a version of the css.php that's designed for the form builder. The difference is that this
 * is passed the placeholder info directly from the builder, not pulling it from the database.
 *
 *  THIS FILE IS A MESS. THINK IT THROUGH AGAIN.
 */

header("Content-Type: text/css");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

$g_check_ft_sessions = false;
require_once(realpath(dirname(__FILE__) . "/../../../../global/library.php"));
ft_include_module("form_builder");

$resource_id = $_GET["resource_id"];
$source = (isset($_GET["source"]) && $_GET["source"] == "sessions") ? "sessions" : "database";

$resource_info = fb_get_resource($resource_id);
$set_id = $resource_info["template_set_id"];
$css    = $resource_info["content"];

$placeholders = fb_get_placeholders($set_id);
$placeholder_hash = array();
foreach ($placeholders as $placeholder_info)
{
  $placeholder_hash[$placeholder_info["placeholder_id"]] = $placeholder_info["placeholder"];
}

$config = array();
$smarty = fb_create_new_smarty_instance();

$P = array();
if ($source == "sessions")
{
  if ($g_session_type == "database")
  {
    $sess = new SessionManager();
  }
  if (!empty($g_session_save_path))
    session_save_path($g_session_save_path);

  session_start();
  header("Cache-control: private");
  header("Content-Type: text/html; charset=utf-8");

  $placeholder_id_to_values = $_SESSION["ft"]["form_builder"]["placeholders"];

  while (list($placeholder_id, $value) = each($placeholder_id_to_values))
  {
    if (!isset($placeholder_hash[$placeholder_id]))
      continue;

    $placeholder = $placeholder_hash[$placeholder_id];

    // TODO multi-select + checkboxes...
    $P[$placeholder] = $value;
  }
}
else
{
  $config = fb_get_form_configuration($_GET["published_form_id"]);
  foreach ($config["placeholders"] as $placeholder_info)
  {
    $curr_placeholder_id = $placeholder_info["placeholder_id"];
    $val = $placeholder_info["placeholder_value"];

    if (!isset($placeholder_hash[$curr_placeholder_id]))
      continue;

    $placeholder = $placeholder_hash[$curr_placeholder_id];

    $P[$placeholder] = $val;
  }
}

$smarty->assign("P", $P);

$smarty->assign("eval_str", $css);
echo $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");





































