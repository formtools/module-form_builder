<?php

use FormTools\Modules\FormBuilder\Resources;
use FormTools\Modules\FormBuilder\TemplateSets;

$sortable_id = "resources_list";

if (isset($_GET["delete"])) {
    list($g_success, $g_message) = Resources::deleteResource($_GET["delete"], $L);
}
if (isset($_POST["update_order"])) {
    $_POST["sortable_id"] = $sortable_id;
    list($g_success, $g_message) = Resources::updateResourceOrder($_POST, $L);
}

$template_set_info = TemplateSets::getTemplateSet($set_id);
$resources = Resources::getResources($set_id);

$page_vars["sortable_id"] = $sortable_id;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["resources"] = $resources;
$page_vars["js_messages"] = array(
    "word_close", "word_yes", "word_no", "word_cancel", "phrase_please_confirm"
);
$page_vars["module_js_messages"] = array(
    "confirm_delete_resource",
    "text_template_set_complete",
    "phrase_template_set_status",
    "text_template_set_incomplete"
);
$page_vars["head_js"] = <<< END
$(function() {
  fb_ns.init_add_resource();
  fb_ns.init_template_status_dialog();
});
END;

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
