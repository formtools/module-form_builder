<?php

require_once("../../global/library.php");
ft_init_module_page();
$request = array_merge($_POST, $_GET);
$sortable_id = "template_set_table";

// hidden feature, used for development only
if (isset($_GET["generate"]))
{
  echo fb_create_default_template_set_file();
	exit;
}

if (isset($_GET["export"]) && is_numeric($_GET["export"]))
{
  echo fb_generate_template_set_export_file($_GET["export"]);
	exit;
}

if (isset($_GET["import"]))
{
  list($g_success, $g_message) = fb_import_template_set($_GET["import"]);
}

if (isset($_GET["delete"]))
{
  list($g_success, $g_message) = fb_delete_template_set($_GET["delete"]);
}
if (isset($request["update_order"]))
{
  $request["sortable_id"] = $sortable_id;
  list($g_success, $g_message) = fb_update_template_set_order($request);
}

$template_sets = fb_get_template_sets(false);

// shame, but needed for the markup
$updated_template_sets = array();
foreach ($template_sets as $template_set_info)
{
  $set_id = $template_set_info["set_id"];
  $template_set_info["usage"] = fb_get_template_set_usage($set_id);
  $updated_template_sets[] = $template_set_info;
}

$module_settings = ft_get_module_settings("", "form_builder");

$page_vars = array();
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_sets"] = $updated_template_sets;
$page_vars["module_settings"] = $module_settings;
$page_vars["js_messages"] = array("word_close", "word_yes", "word_no", "phrase_please_confirm");
$page_vars["module_js_messages"] = array("phrase_create_new_template_set", "validation_no_template_set_name",
  "confirm_delete_template_set", "confirm_delete_template_set");

$page_vars["head_string"] =<<< END
  <script src="$g_root_url/global/scripts/sortable.js"></script>
  <script src="global/scripts/manage_template_sets.js"></script>
  <link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css">
END;

$page_vars["head_js"] =<<< END
$(function() {
  fb_ns.init_create_new_template_set_dialog();
  $(".info").bind("click", function() {
    ft.create_dialog({
      title: "{$L["phrase_delete_template_set"]}",
      content: "{$L["text_delete_template_in_use"]}",
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

ft_display_module_page("templates/index.tpl", $page_vars);
