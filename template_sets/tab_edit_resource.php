<?php

$resource_id = ft_load_module_field("form_builder", "resource_id", "resource_id");

if (isset($request["update"]))
{
  list($g_success, $g_message) = fb_update_resource($request["resource_id"], $request);
}

$template_set_info = fb_get_template_set($set_id);
$resource_info     = fb_get_resource($resource_id);

$text_resource_placeholder_hint = ft_eval_smarty_string($L["text_resource_placeholder_hint"], array("var" => "{{\$R." . $resource_info["placeholder"] . "}}"));

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=resources&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=resources&set_id={$links["next_set_id"]}" : "";

$page_vars["resource_id"] = $resource_id;
$page_vars["text_resource_placeholder_hint"] = $text_resource_placeholder_hint;
$page_vars["head_title"] = $L["phrase_edit_resource"];
$page_vars["template_set_info"] = $template_set_info;
$page_vars["resource_info"] = $resource_info;
$page_vars["head_string"] =<<< END
<script src="$g_root_url/global/scripts/sortable.js"></script>
<script src="$g_root_url/modules/form_builder/global/scripts/manage_template_sets.js"></script>
<link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css"></link>
<script src="$g_root_url/global/codemirror/js/codemirror.js"></script>
END;

$page_vars["js_messages"] = array("word_delete", "word_close");
$page_vars["module_js_messages"] = array("text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");
$page_vars["head_js"] =<<< EOF
var rules = [];
$(function() {
  fb_ns.init_template_status_dialog();
});
EOF;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
