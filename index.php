<?php

require_once("../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\TemplateSets;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$LANG = Core::$L;
$root_url = Core::getRootUrl();

$sortable_id = "template_set_table";

// hidden feature, used for development only
if (isset($_GET["generate"])) {
    echo TemplateSets::createDefaultTemplateSetFile();
    exit;
}

if (isset($_GET["export"]) && is_numeric($_GET["export"])) {
    echo TemplateSets::generateTemplateSetExportFile($_GET["export"]);
    exit;
}

$success = true;
$message = "";
if (isset($_GET["import"])) {
    list($success, $message) = TemplateSets::importTemplateSet($_GET["import"], $L);
}
if (isset($_GET["delete"])) {
    list($success, $message) = TemplateSets::deleteTemplateSet($_GET["delete"], $L);
}
if (isset($request["update_order"])) {
    $request["sortable_id"] = $sortable_id;
    list($success, $message) = TemplateSets::updateTemplateSetOrder($request, $L);
}

$template_sets = TemplateSets::getTemplateSets(false);

// shame, but needed for the markup
$updated_template_sets = array();
foreach ($template_sets as $template_set_info) {
    $set_id = $template_set_info["set_id"];
    $template_set_info["usage"] = TemplateSets::getTemplateSetUsage($set_id);
    $updated_template_sets[] = $template_set_info;
}

$module_settings = $module->getSettings();

$page_vars = array(
    "g_success" => $success,
    "g_message" => $message,
    "sortable_id" => $sortable_id,
    "template_sets" => $updated_template_sets,
    "module_settings" => $module_settings,
    "js_messages" => array(
        "word_close", "word_yes", "word_no", "phrase_please_confirm"
    ),
    "module_js_messages" => array(
        "phrase_create_new_template_set", "validation_no_template_set_name",
        "confirm_delete_template_set", "confirm_delete_template_set"
    )
);

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

$module->displayPage("templates/index.tpl", $page_vars);
