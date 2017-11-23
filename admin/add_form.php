<?php

require("../../../global/library.php");

use FormTools\Core;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\General;
use FormTools\Pages;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$LANG = Core::$L;

if (isset($request["add_form"])) {
    list($g_success, $g_message, $new_form_id) = General::createForm($request);
    if ($g_success) {
        header("location: ../../../admin/forms/edit/?form_id={$new_form_id}&message=notify_form_builder_form_created");
        exit;
    }
}

$page_values = array(
    "page" => "add_form_internal",
    "page_url" => Pages::getPageUrl("add_form_internal"),
    "head_title" => "{$LANG['phrase_add_form']}",
    "L" => $L
);

$page_vars["head_js"] =<<< END
ft.click([
  { el: "at1", targets: [{ el: "custom_clients", action: "hide" }] },
  { el: "at2", targets: [{ el: "custom_clients", action: "hide" }] },
  { el: "at3", targets: [{ el: "custom_clients", action: "show" }] }
]);

$(function() {
  $("#form_name").focus();
  $("#create_internal_form").bind("submit",function(e) {
    var rules = [];
    rules.push("required,form_name,{$LANG["validation_no_form_name"]}");
    rules.push("required,num_fields,{$LANG["validation_no_num_form_fields"]}");
    rules.push("digits_only,num_fields,{$LANG["validation_invalid_num_form_fields"]}");
    rules.push("range<=1000,num_fields,{$LANG["validation_internal_form_too_many_fields"]}");
    rules.push("required,access_type,{$LANG["validation_no_access_type"]}");
    if (!rsv.validate(this, rules)) {
      e.preventDefault();
    }
    ft.select_all("selected_client_ids[]");
  });
});
END;

$module->displayPage("../../modules/form_builder/templates/admin/add_form.tpl", $page_vars);
