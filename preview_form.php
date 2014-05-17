<?php

/**
 * This page handles the actual generation of the form, for use in the Form Builder window. It works
 * quite simply: it relies entirely on the calling page passing in all the values it needs to generate
 * and render the page.
 */

require_once("../../global/library.php");
ft_init_module_page();
$request = array_merge($_POST, $_GET);
$request = ft_undo_magic_quotes($request);

// convert the placeholder info in the request into a simple hash of placeholder_id => value
$placeholders = array();
$placeholder_ids = isset($request["placeholder_ids"]) ? $request["placeholder_ids"] : array();
foreach ($placeholder_ids as $placeholder_id)
{
	// note: this will either store a string or an array (checkboxes / multi-select)
  $placeholders[$placeholder_id] = (isset($request["placeholder_{$placeholder_id}"])) ? $request["placeholder_{$placeholder_id}"] : "";
}

// we store the placeholders in sessions so that any resources (CSS/JS) in Smarty format can access the info. Normally it's just
// pulled from the database
$_SESSION["ft"]["form_builder"]["placeholders"] = $placeholders;

// creating a new array here isn't strictly needed, since we COULD just tweak and pass along the POST request,
// but it helps to see precisely what info is being sent
$settings = array(

  // used by the generate function for things like overriding the default form submit, and
  // other functionality when in preview mode
  "mode" => "preview", // "preview" / "live"

  // main info
  "form_id"                    => $request["form_id"],
  "view_id"                    => $request["view_id"],
  "submission_id"              => "", // N/A during the form building process
  "template_set_id"            => $request["template_set_id"],
  "page"                       => $request["page"],
  "include_review_page"        => isset($request["include_review_page"]) ? true : false,
  "include_thanks_page_in_nav" => isset($request["include_thanks_page_in_nav"]) ? true : false,
  "is_online"                  => isset($request["is_online"]) ? true : false,

  // the thankyou and form offline page content
  "thankyou_page_content"     => $request["thankyou_page_content"],
  "form_offline_page_content" => $request["form_offline_page_content"],

  // other
  "offline_date"        => $request["offline_date"],
  "review_page_title"   => $request["review_page_title"],
  "thankyou_page_title" => $request["thankyou_page_title"],

  // templates
  "page_layout_template_id"       => $request["page_layout_template_id"],
  "header_template_id"            => $request["header_template_id"],
  "footer_template_id"            => $request["footer_template_id"],
  "navigation_template_id"        => $request["navigation_template_id"],
  "continue_block_template_id"    => $request["continue_block_template_id"],
  "error_message_template_id"     => $request["error_message_template_id"],
  "form_page_template_id"         => $request["form_page_template_id"],
  "form_offline_page_template_id" => $request["form_offline_page_template_id"],
  "review_page_template_id"       => $request["review_page_template_id"],
  "thankyou_page_template_id"     => $request["thankyou_page_template_id"],

  // placeholders
  "placeholders" => $placeholders,

);

echo fb_generate_form($settings);

