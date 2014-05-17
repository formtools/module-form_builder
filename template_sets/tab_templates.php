<?php

$sortable_id = "template_list";

if (isset($request["delete"]))
{
  list($g_success, $g_message) = fb_delete_template($request["delete"], $request);
}
else if (isset($request["update_order"]))
{
	$request["sortable_id"] = $sortable_id;
  list($g_success, $g_message) = fb_update_template_order($set_id, $request);
}

$template_set_info = fb_get_template_set($set_id, array("get_template_usage" => true));

$missing_templates = fb_get_missing_template_set_templates($set_id);
$missing_template_strs = array();
foreach ($missing_templates as $template_type)
{
	$missing_template_strs[] = fb_get_template_type_name($template_type);
}
$missing_templates_str = implode(", ", $missing_template_strs);

$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["missing_templates_str"] = $missing_templates_str;

$page_vars["js_messages"] = array("word_close", "word_yes", "word_no", "phrase_please_confirm");
$page_vars["module_js_messages"] = array("confirm_delete_template", "phrase_create_new_template", "validation_no_template_name",
  "validation_no_source_template", "text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");

$page_vars["head_string"] =<<< END
  <script src="$g_root_url/global/scripts/sortable.js"></script>
  <script src="../global/scripts/manage_template_sets.js"></script>
  <link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css"></link>
END;
$page_vars["head_js"] =<<< END
$(function() {
  fb_ns.init_create_new_template_dialog();
  fb_ns.init_template_status_dialog();

  $(".info").bind("click", function() {
    ft.create_dialog({
      title:   "{$L["phrase_delete_template_set"]}",
      content: "{$L["notify_delete_template_in_use"]}",
      popup_type: "info",
      width: 460,
      buttons: [{
        text: "{$LANG["word_close"]}",
        click: function() {
          $(this).dialog("close");
        }
      }]
    });
  });
});
END;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
