<?php

require_once("../../global/session_start.php");
ft_init_module_page();
$permissions = ft_check_permission("admin", false); // TODO
$request = array_merge($_POST, $_GET);

$major_error = "";
if (!isset($request["form_id"]) || empty($request["form_id"]))
{
  $major_error = "Sorry, the Form Builder didn't receive the form ID via the query string. Please report this problem in the forums.";
}
else
{
  $form_id = $request["form_id"];
}

// if the URL includes the published form ID, the admin is editing an existing configuration
$published_form_id = "";
if (isset($request["published_form_id"]))
{
	$published_form_id = $request["published_form_id"];
}
$module_settings = ft_get_module_settings("", "form_builder");

$selected_templates = array();
$config_info        = array();
$include_review_page = "yes";
$include_thanks_page_in_nav = "yes";
$is_online = "yes";
$is_published = "no";
$view_id = "";
$thankyou_page_content     = $module_settings["default_thankyou_page_content"];
$form_offline_page_content = $module_settings["default_form_offline_page_content"];
$published_filename    = "";
$published_folder_url  = $module_settings["default_published_folder_url"];
$published_folder_path = $module_settings["default_published_folder_path"];
$review_page_title     = $module_settings["review_page_title"];
$thankyou_page_title   = $module_settings["thankyou_page_title"];
$offline_date          = "";

// if we're editing an existing configuration, override all the defaults with whatever's been saved
if (!empty($published_form_id))
{
  $config_info = fb_get_form_configuration($published_form_id);
  $is_published = $config_info["is_published"];
  $is_online = $config_info["is_online"];
  $set_id    = $config_info["set_id"];
  $view_id   = $config_info["view_id"];
  $include_review_page = $config_info["include_review_page"];
  $thankyou_page_content     = $config_info["thankyou_page_content"];
  $form_offline_page_content = $config_info["form_offline_page_content"];

	foreach ($config_info["templates"] as $template_info)
	{
	  $selected_templates[$template_info["template_type"]] = $template_info["template_id"];
	}
  $published_filename = preg_replace("/\.php$/", "", $config_info["filename"]);
  $published_folder_url  = $config_info["folder_url"];
  $published_folder_path = $config_info["folder_path"];

  if ($config_info["offline_date"] != "0000-00-00 00:00:00")
  {
  	// convert the datetime to a friendlier format
  	list($date, $time) = explode(" ", $config_info["offline_date"]);
  	list($year, $month, $day) = explode("-", $date);
  	list($hours, $mins, $seconds) = explode(":", $time);
  	$offline_date = "{$month}/{$day}/{$year} $hours:$mins";
  }

  $review_page_title     = $config_info["review_page_title"];
  $thankyou_page_title   = $config_info["thankyou_page_title"];
}
else
{
  // here, the admin is publishing a new form. There are no templates or other settings specified yet, so
  // we just pick the first set ID
	$set_id = fb_get_first_template_set_id();
  if (empty($set_id))
  {
    $major_error = $L["notify_no_complete_template"];
  }

  $views = ft_get_form_views($form_id);
  if (!empty($views))
  {
  	$view_id = $views[0]["view_id"];
  }
}

if (!ft_check_view_exists($view_id))
{
  $major_error = "Sorry, the View that was assigned to this form no longer exists. You will need to delete this form configuration and publish a new form.";
}

// calculate the page element heights and widths
$default_width  = $module_settings["form_builder_width"];
$default_height = $module_settings["form_builder_height"];
$sidebar_width = 260;
$header_height = 34;
$footer_height = 30;
$iframe_header_height = 30;
$iframe_width  = $default_width - $sidebar_width;
$content_height = $default_height - ($header_height + $footer_height);

$page_vars = array();
$page_vars["allow_url_fopen"] = (ini_get("allow_url_fopen") == "1");
$page_vars["major_error"] = $major_error;
$page_vars["published_form_id"] = $published_form_id; //
$page_vars["include_review_page"] = $include_review_page;
$page_vars["include_thanks_page_in_nav"] = $include_thanks_page_in_nav;
$page_vars["is_published"] = $is_published;
$page_vars["is_online"] = $is_online;
$page_vars["thankyou_page_content"] = $thankyou_page_content;
$page_vars["form_offline_page_content"] = $form_offline_page_content;
$page_vars["module_settings"] = $module_settings;
$page_vars["header_height"] = $header_height;
$page_vars["footer_height"] = $footer_height;
$page_vars["content_height"] = $content_height;
$page_vars["sidebar_width"] = $sidebar_width;
$page_vars["iframe_width"] = $iframe_width;
$page_vars["iframe_header_height"] = $iframe_header_height;
$page_vars["published_filename"] = $published_filename;
$page_vars["published_folder_url"] = $published_folder_url;
$page_vars["published_folder_path"] = $published_folder_path;
$page_vars["offline_date"] = $offline_date;
$page_vars["review_page_title"] = $review_page_title;
$page_vars["thankyou_page_title"] = $thankyou_page_title;

if (empty($major_error))
{
  $placeholders = fb_get_placeholders($set_id);
	$page_vars["form_id"] = $form_id;
	$page_vars["view_id"] = $view_id;
	$page_vars["set_id"]  = $set_id;
	$page_vars["selected_templates"] = $selected_templates;
	$page_vars["placeholders"] = $placeholders;
	$page_vars["js"] = "g.view_tabs = [" . fb_get_num_view_tabs_js($form_id) . "];";

	$placeholder_hash = array();
	if (empty($config_info))
	{
		foreach ($placeholders as $placeholder_info)
		{
			$placeholder_hash[$placeholder_info["placeholder_id"]] = $placeholder_info["default_value"];
		}
	}
	else
	{
    foreach ($config_info["placeholders"] as $placeholder_info)
    {
    	$placeholder_hash[$placeholder_info["placeholder_id"]] = $placeholder_info["placeholder_value"];
    }
	}

	$page_vars["placeholder_hash"] = $placeholder_hash;
}

ft_display_module_page("templates/preview.tpl", $page_vars);
