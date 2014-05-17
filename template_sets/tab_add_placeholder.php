<?php

$sortable_id = "placeholder_option_list";
$set_id = ft_load_module_field("form_builder", "set_id", "set_id");
$placeholder_id = ft_load_module_field("form_builder", "placeholder_id", "placeholder_id");

if (isset($request["add_placeholder"]))
{
	$request["sortable_id"] = $sortable_id;
  list($g_success, $g_message) = fb_add_placeholder($set_id, $request);
  if ($g_success)
  {
  	header("location: index.php?page=placeholders&msg=placeholder_added");
  	exit;
  }
}

$template_set_info = fb_get_template_set($set_id);

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=placeholders&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=placeholders&set_id={$links["next_set_id"]}" : "";

$page_vars["head_title"] = $L["phrase_add_placeholder"];
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["head_string"] =<<< END
<script src="$g_root_url/global/scripts/sortable.js"></script>
<script src="$g_root_url/modules/form_builder/global/scripts/manage_template_sets.js"></script>
<link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css"></link>
END;

$page_vars["js_messages"] = array("word_delete", "word_close");
$page_vars["module_js_messages"] = array("text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");
$page_vars["head_js"] =<<< EOF
var rules = [];
$(function() {
  fb_ns.add_placeholder_row();
  $("#field_type").val("").bind("change keyup", function() {
    fb_ns.change_field_type(this.value);
  });
  fb_ns.init_template_status_dialog();
});
EOF;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
