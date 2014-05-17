<?php

$sortable_id = "resources_list";

if (isset($_GET["delete"]))
{
	list($g_success, $g_message) = fb_delete_resource($_GET["delete"]);
}
if (isset($_POST["update_order"]))
{
	$_POST["sortable_id"] = $sortable_id;
  list($g_success, $g_message) = fb_update_resource_order($set_id, $_POST);
}

$template_set_info = fb_get_template_set($set_id);
$resources = fb_get_resources($set_id);

$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["resources"] = $resources;
$page_vars["js_messages"] = array("word_close", "word_yes", "word_no", "word_cancel", "phrase_please_confirm");
$page_vars["module_js_messages"] = array("confirm_delete_resource", "text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");
$page_vars["head_string"] =<<< END
  <script src="$g_root_url/global/scripts/sortable.js"></script>
  <script src="../global/scripts/manage_template_sets.js"></script>
  <link type="text/css" rel="stylesheet" href="{$g_root_url}/modules/form_builder/global/css/styles.css">
END;
$page_vars["head_js"] =<<< END
$(function() {
  fb_ns.init_add_resource();
  fb_ns.init_template_status_dialog();
});
END;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
