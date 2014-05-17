<?php

if (isset($request["update"]))
{
  list($g_success, $g_message) = fb_update_template_set_info($set_id, $request);
}

$template_set_info = fb_get_template_set($set_id);

$missing_templates = fb_get_missing_template_set_templates($set_id);
$missing_template_strs = array();
foreach ($missing_templates as $template_type)
{
	$missing_template_strs[] = fb_get_template_type_name($template_type);
}
$missing_templates_str = implode(", ", $missing_template_strs);

$usage = fb_get_template_set_usage($set_id);

$page_vars["missing_templates_str"] = $missing_templates_str;
$page_vars["usage"] = $usage;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["js_messages"] = array("word_close", "word_yes", "word_no", "phrase_open_form_in_new_tab_or_win");
$page_vars["module_js_messages"] = array("text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");

$page_vars["head_string"] =<<< END
  <script src="../global/scripts/manage_template_sets.js"></script>
  <script src="$g_root_url/global/codemirror/js/codemirror.js"></script>
  <link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css"></link>
END;

$page_vars["head_js"] =<<< END
$(function() {
  ft.init_show_form_links();
  fb_ns.init_template_status_dialog();
});
END;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
