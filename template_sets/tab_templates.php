<?php

use FormTools\Modules\FormBuilder\Templates;
use FormTools\Modules\FormBuilder\TemplateSets;

$sortable_id = "template_list";

$success = true;
$message = "";
if (isset($request["delete"])) {
    list($success, $message) = Templates::deleteTemplate($request["delete"], $L);
} else {
    if (isset($request["update_order"])) {
        $request["sortable_id"] = $sortable_id;
        list($success, $message) = Templates::updateTemplateOrder($request, $L);
    }
}

$template_set_info = TemplateSets::getTemplateSet($set_id, array("get_template_usage" => true));

$missing_templates = TemplateSets::getMissingTemplateSetTemplates($set_id);
$missing_template_strs = array();
foreach ($missing_templates as $template_type) {
    $missing_template_strs[] = TemplateSets::getTemplateTypeName($template_type, $L);
}
$missing_templates_str = implode(", ", $missing_template_strs);

$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["missing_templates_str"] = $missing_templates_str;

$page_vars["js_messages"] = array(
    "word_close", "word_yes", "word_no", "phrase_please_confirm"
);
$page_vars["module_js_messages"] = array(
    "confirm_delete_template",
    "phrase_create_new_template",
    "validation_no_template_name",
    "validation_no_source_template",
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< END
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

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
