<?php

use FormTools\Modules;
use FormTools\Modules\FormBuilder\Placeholders;
use FormTools\Modules\FormBuilder\TemplateSets;

$sortable_id = "placeholder_option_list";
$set_id = Modules::loadModuleField("form_builder", "set_id", "set_id");
$placeholder_id = Modules::loadModuleField("form_builder", "placeholder_id", "placeholder_id");

$success = true;
$message = "";
if (isset($request["add_placeholder"])) {
    list($success, $message) = Placeholders::addPlaceholder($set_id, $request["placeholder_label"], $request["placeholder"],
        $request["field_type"], $request["field_orientation"], $request["default_value"]);
    if ($success) {
        header("location: index.php?page=placeholders&msg=placeholder_added");
        exit;
    }
}

$template_set_info = TemplateSets::getTemplateSet($set_id);

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=placeholders&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=placeholders&set_id={$links["next_set_id"]}" : "";
$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["head_title"] = $L["phrase_add_placeholder"];
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["js_messages"] = array(
    "word_delete", "word_close"
);
$page_vars["module_js_messages"] = array(
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< EOF
var rules = [];
$(function() {
  fb_ns.add_placeholder_row();
  $("#field_type").val("").bind("change keyup", function() {
    fb_ns.change_field_type(this.value);
  });
  fb_ns.init_template_status_dialog();
});
EOF;

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
