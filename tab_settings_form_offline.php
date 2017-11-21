<?php

$success = true;
$message = "";
if (isset($request["update"])) {
    $settings = array(
        "default_form_offline_page_content" => $request["default_form_offline_page_content"],
        "scheduled_offline_form_behaviour" => $request["scheduled_offline_form_behaviour"]
    );
    $module->setSettings($settings);

    $success = true;
    $message = $L["notify_settings_updated"];
}

$page_vars["g_success"] = $success;
$page_vars["g_message"] = $message;
$page_vars["module_settings"] = $module->getSettings();

$module->displayPage("templates/settings.tpl", $page_vars);
