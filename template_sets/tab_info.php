<?php

use FormTools\Modules\FormBuilder\TemplateSets;

$success = true;
$message = "";
if (isset($request["update"])) {
    list($success, $message) = TemplateSets::updateTemplateSetInfo($set_id, $request["set_name"],
        $request["description"], $request["version"], $L);
}

$template_set_info = TemplateSets::getTemplateSet($set_id);

$missing_templates = TemplateSets::getMissingTemplateSetTemplates($set_id);
$missing_template_strs = array();
foreach ($missing_templates as $template_type) {
    $missing_template_strs[] = TemplateSets::getTemplateTypeName($template_type, $L);
}
$missing_templates_str = implode(", ", $missing_template_strs);

$usage = TemplateSets::getTemplateSetUsage($set_id);

$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["missing_templates_str"] = $missing_templates_str;
$page_vars["usage"] = $usage;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["js_messages"] = array(
    "word_close", "word_yes", "word_no", "phrase_open_form_in_new_tab_or_win"
);
$page_vars["module_js_messages"] = array(
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< END
$(function() {
  ft.init_show_form_links();
  fb_ns.init_template_status_dialog();
});
END;

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
