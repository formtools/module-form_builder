<?php

require_once("../../../../global/library.php");
ft_init_module_page();

// TODO permissions

// the action to take and the ID of the page where it will be displayed (allows for
// multiple calls on same page to load content in unique areas)
$request = array_merge($_POST, $_GET);
$action  = $request["action"];

// Find out if we need to return anything back with the response. This mechanism allows us to pass any information
// between the Ajax submit function and the Ajax return function. Usage:
//   "return_vals[]=question1:answer1&return_vals[]=question2:answer2&..."
$return_val_str = "";
if (isset($request["return_vals"]))
{
  $vals = array();
  foreach ($request["return_vals"] as $pair)
  {
    list($key, $value) = split(":", $pair);
    $vals[] = "$key: \"$value\"";
  }
  $return_val_str = ", " . join(", ", $vals);
}


switch ($action)
{
  case "create_new_template_set":
    $template_set_name = $request["template_set_name"];
    $original_set_id   = isset($request["original_set_id"]) ? $request["original_set_id"] : "";
    $results = fb_create_new_template_set($template_set_name, $original_set_id);
    echo ft_convert_to_json($results);
    break;

  case "create_new_template":
    $set_id = $request["set_id"];
    $results = fb_create_new_template($set_id, $request);
    echo ft_convert_to_json($results);
    break;

  case "add_resource":
    $set_id = $request["set_id"];
    $resource_name = $request["resource_name"];
    $placeholder   = $request["placeholder"];
    $resource_type = $request["resource_type"];
    $result = fb_add_resource($set_id, $resource_name, $placeholder, $resource_type);
    echo ft_convert_to_json($result);
    break;

  case "save_builder_settings":
    $result = fb_save_builder_settings($request);
    echo ft_convert_to_json($result);
    break;

  case "get_template_set_templates_html":
    $set_id = $request["set_id"];
    echo fb_generate_template_set_templates_html($set_id);
    break;

  case "get_template_set_placeholders_html":
    $set_id = $request["set_id"];
    $placeholders = fb_get_placeholders($set_id);

    // set the default values
    $placeholder_hash = array();
    foreach ($placeholders as $p_info)
    {
      $placeholder_hash[$p_info["placeholder_id"]] = $p_info["default_value"];
    }
    echo fb_generate_template_set_placeholders_html($set_id, $placeholders, $placeholder_hash);
    break;

  case "publish_form":
    $module_settings = ft_get_module_settings("", "form_builder");
    if ($module_settings["demo_mode"] != "on")
    {
      $result = fb_publish_form($request);
      echo ft_convert_to_json($result);
    }
    break;

  case "update_publish_settings":
    $module_settings = ft_get_module_settings("", "form_builder");
    if ($module_settings["demo_mode"] != "on")
    {
      $result = fb_update_publish_settings($request);
      echo ft_convert_to_json($result);
    }
    break;
}
