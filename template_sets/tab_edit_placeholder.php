<?php

$sortable_id = "placeholder_option_list";
$set_id = ft_load_module_field("form_builder", "set_id", "set_id");
$placeholder_id = ft_load_module_field("form_builder", "placeholder_id", "placeholder_id");

if (isset($_POST["update"]))
{
  list($g_success, $g_message) = fb_update_placeholder($_POST["placeholder_id"], $_POST);
}

$template_set_info = fb_get_template_set($set_id);
$placeholder_info  = fb_get_placeholder($placeholder_id);

$text_placeholder_hint = ft_eval_smarty_string($L["text_placeholder_hint"], array("var" => "{{\$P." . $placeholder_info["placeholder"] . "}}"));

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=placeholders&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=placeholders&set_id={$links["next_set_id"]}" : "";

$page_vars["placeholder_id"] = $placeholder_id;
$page_vars["text_placeholder_hint"] = $text_placeholder_hint;
$page_vars["head_title"] = $L["phrase_edit_placeholder"];
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["placeholder_info"] = $placeholder_info;
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
  $("#field_type").bind("change keyup", function() {
    fb_ns.change_field_type(this.value);
  });

  // if there are no placeholder option rows, add a default one
  //fb_ns.add_placeholder_row(); TODO
  fb_ns.init_template_status_dialog();
});
EOF;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
