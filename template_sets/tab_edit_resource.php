<?php

use FormTools\General;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\Resources;
use FormTools\Modules\FormBuilder\TemplateSets;

$resource_id = Modules::loadModuleField("form_builder", "resource_id", "resource_id");

$success = true;
$message = "";
if (isset($request["update"])) {
    list($success, $message) = Resources::updateResource($request["resource_id"], $request, $L);
}

$template_set_info = TemplateSets::getTemplateSet($set_id);
$resource_info = Resources::getResource($resource_id);

$text_resource_placeholder_hint = General::evalSmartyString($L["text_resource_placeholder_hint"],
    array("var" => "{{\$R." . $resource_info["placeholder"] . "}}"));

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=resources&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=resources&set_id={$links["next_set_id"]}" : "";

$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["resource_id"] = $resource_id;
$page_vars["text_resource_placeholder_hint"] = $text_resource_placeholder_hint;
$page_vars["head_title"] = $L["phrase_edit_resource"];
$page_vars["template_set_info"] = $template_set_info;
$page_vars["resource_info"] = $resource_info;
$page_vars["js_messages"] = array("word_delete", "word_close");
$page_vars["module_js_messages"] = array(
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< END
var rules = [];
$(function() {
  fb_ns.init_template_status_dialog();
});
END;

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
