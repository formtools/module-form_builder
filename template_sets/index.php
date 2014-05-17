<?php

require_once("../../../global/library.php");
ft_init_module_page();
$request = array_merge($_POST, $_GET);

$set_id = ft_load_module_field("form_builder", "set_id", "set_id");

/*
// store the current selected tab in memory - except for pages which require additional
// query string info. For those, use the parent page
if (isset($request["page"]) && !empty($request["page"]))
{
  $remember_page = $request["page"];
  switch ($remember_page)
  {
    case "edit_template":
      $remember_page = "templates";
      break;
    case "edit_email":
      $remember_page = "emails";
      break;
  }

  $_SESSION["ft"]["form_builder"]["edit_template_set"] = $remember_page;
  $page = $request["page"];

  echo $page;
}
else
{
*/

$page = ft_load_module_field("form_builder", "page", "edit_template_set", "templates");


$same_page = ft_get_clean_php_self();
$tabs = array(
  "info" => array(
    "tab_label" => $L["word_info"],
    "tab_link" => "{$same_page}?page=info&set_id={$set_id}",
    "pages" => array("info")
  ),
  "templates" => array(
    "tab_label" => $L["word_templates"],
    "tab_link" => "{$same_page}?page=templates&set_id={$set_id}",
    "pages" => array("templates", "edit_template")
  ),
  "resources" => array(
    "tab_label" => $L["word_resources"],
    "tab_link" => "{$same_page}?page=resources&set_id={$set_id}",
    "pages" => array("resources", "edit_resource")
  ),
  "placeholders" => array(
    "tab_label" => $L["word_placeholders"],
    "tab_link" => "{$same_page}?page=placeholders&set_id={$set_id}",
    "pages" => array("placeholders", "add_placeholder", "edit_placeholder")
  )
);


$links = fb_get_template_set_prev_next_links($set_id);
$prev_tabset_link = (!empty($links["prev_set_id"])) ? "index.php?page=$page&set_id={$links["prev_set_id"]}" : "";
$next_tabset_link = (!empty($links["next_set_id"])) ? "index.php?page=$page&set_id={$links["next_set_id"]}" : "";

// start compiling the page vars here (save duplicate code!)
$page_vars = array();
$page_vars["set_id"] = $set_id;
$page_vars["page"] = $page;
$page_vars["tabs"] = $tabs;
$page_vars["show_tabset_nav_links"] = true;
$page_vars["prev_tabset_link"] = $prev_tabset_link;
$page_vars["next_tabset_link"] = $next_tabset_link;
$page_vars["prev_tabset_link_label"] = $L["phrase_prev_template_set"];
$page_vars["next_tabset_link_label"] = $L["phrase_next_template_set"];

switch ($page)
{
  case "info":
    include("tab_info.php");
    break;
	case "templates":
    include("tab_templates.php");
    break;
  case "edit_template":
    include("tab_edit_template.php");
    break;
  case "resources":
    include("tab_resources.php");
    break;
  case "edit_resource":
    include("tab_edit_resource.php");
    break;
  case "placeholders":
    include("tab_placeholders.php");
    break;
  case "add_placeholder":
    include("tab_add_placeholder.php");
    break;
  case "edit_placeholder":
    include("tab_edit_placeholder.php");
    break;

  default:
    include("tab_info.php");
    break;
}

