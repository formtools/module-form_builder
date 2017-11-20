<?php

use FormTools\Modules\FormBuilder\Placeholders;
use FormTools\Modules\FormBuilder\TemplateSets;

$sortable_id = "placeholder_list";

$success = true;
$message = "";
if (isset($_GET["delete"])) {
    list($success, $message) = Placeholders::deletePlaceholder($_GET["delete"], $L);
} else {
    if (isset($request["update"])) {
        $request["sortable_id"] = $sortable_id;
        list($success, $message) = Placeholders::updatePlaceholders($request, $L);
    }
}

$template_set_info = TemplateSets::getTemplateSet($set_id);

if (isset($_GET["msg"]) && $_GET["msg"] == "placeholder_added") {
    $g_success = true;
    $g_message = $L["notify_placeholder_added"];
}

$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["placeholders"] = Placeholders::getPlaceholders($set_id);
$page_vars["js_messages"] = array("word_close", "word_yes", "word_no", "phrase_please_confirm");
$page_vars["module_js_messages"] = array(
    "confirm_delete_placeholder",
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< END
$(function() {
  fb_ns.init_template_status_dialog();
});
END;

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
