<?php

use FormTools\Core;
use FormTools\Forms as CoreForms;
use FormTools\General as CoreGeneral;
use FormTools\Pages;
use FormTools\Settings;
use FormTools\Themes;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\Forms;

/**
 * This page is a bit unusual. It handles some of the functionality and Smarty var loading, but the
 * actual tab content is created with /global/code/hooks.php -> fb_display_publish_tab().
 */

$settings = Settings::get();
$module = Modules::getModuleInstance("form_builder");
$L = $module->getLangStrings();
$root_url = Core::getRootUrl();

$success = true;
$message = "";
if (isset($_POST["set_as_form_builder"])) {
    list($success, $message) = Forms::convertFormToFormBuilderForm($form_id, $L);
} else {
    if (isset($_GET["delete"])) {
        list($success, $message) = Forms::deleteFormConfiguration($form_id, $_GET["delete"], $L);
    } else {
        if (isset($_GET["delete_published_form"])) {
            $override = (isset($_GET["override"])) ? true : false;
            $published_form_id = $_GET["delete_published_form"];
            list($success, $message) = $module->deletePublishedForm($form_id, $published_form_id, $_GET["delete_form_config"], $L, $override);
        } else {
            if (isset($_POST["update_order"])) {
                list($success, $message) = Forms::updatePublishedFormOrder($form_id, $L, $_POST);
            }
        }
    }
}

$form_info = CoreForms::getForm($form_id);

$module_settings = $module->getSettings();
$width = $module_settings["form_builder_width"];
$height = $module_settings["form_builder_height"];

// compile the templates information
$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["page"] = "publish";
$page_vars["page_url"] = Pages::getPageUrl("edit_form_main", array("form_id" => $form_id));
$page_vars["head_title"] = "{$LANG["phrase_edit_form"]} - {$L["word_publish"]}";
$page_vars["form_info"] = $form_info;
$page_vars["js_messages"] = array(
    "word_cancel",
    "phrase_please_confirm",
    "word_yes",
    "word_no",
    "phrase_show_form",
    "word_close",
    "phrase_open_form_in_new_tab_or_win"
);

$page_vars["head_string"] = <<< END
<script src="$root_url/global/scripts/manage_forms.js?v=2"></script>
<script src="$root_url/global/scripts/sortable.js"></script>
<link type="text/css" rel="stylesheet" href="$root_url/modules/form_builder/css/edit_form.css">
<script src="{$root_url}/modules/form_builder/scripts/manage_forms.js"></script>
END;

$default_timezone_offset = $settings["default_timezone_offset"];
$server_time = CoreGeneral::getDate($default_timezone_offset, CoreGeneral::getCurrentDatetime(), "M jS, Y G:i:s A");

$onload_js = "";
if (isset($_GET["action"]) && $_GET["action"] == "auto_open" && isset($_GET["published_form_id"])) {
    $published_form_id = $_GET["published_form_id"];
    $onload_js = "dialog = window.open(\"$root_url/modules/form_builder/preview.php?form_id=$form_id&published_form_id=$published_form_id\", \"preview\", \"status=0,toolbar=0,location=0,menubar=0,height=$height,width=$width\");";
}

$page_vars["head_js"] = <<< END
$(function() {
  var dialog = null;
  $("#publish_new_form").bind("click", function() {
    if (dialog == null || dialog.closed) {
      var dialog = window.open("$root_url/modules/form_builder/preview.php?form_id=$form_id", "preview", "status=0,toolbar=0,location=0,menubar=0,height=$height,width=$width");
    } else {
      dialog.focus();
    }
  });

  $("#form_builder_form_list .edit").bind("click", function() {
    if (dialog == null || dialog.closed) {
      var published_form_id = $(this).closest(".row_group").find(".sr_order").val();
      dialog = window.open("$root_url/modules/form_builder/preview.php?form_id=$form_id&published_form_id=" + published_form_id, "preview", "status=0,toolbar=0,location=0,menubar=0,height=$height,width=$width");
    } else {
      dialog.focus();
    }
  });

  ft.init_show_form_links();
  $onload_js
});

g.mod_messages = {
  confirm_delete_form_confirmation: "{$L["confirm_delete_form_confirmation"]}",
  phrase_form_offline_date: "{$L["phrase_form_offline_date"]}",
  notify_form_offline_message: "{$L["notify_form_offline_message"]}"
}
g.current_server_time = "$server_time";

END;

// N.B. the actual tab_publish.tpl is loaded via a hook
Themes::displayPage("admin/forms/edit/index.tpl", $page_vars);
