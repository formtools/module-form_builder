<?php

use FormTools\Modules;

$success = true;
$message = "";
if (isset($request["update"])) {
    $settings = array(
        "default_published_folder_path" => $request["default_published_folder_path"],
        "default_published_folder_url"  => $request["default_published_folder_url"],
        "review_page_title"             => $request["review_page_title"],
        "thankyou_page_title"           => $request["thankyou_page_title"],
        "form_builder_width"            => $request["form_builder_width"],
        "form_builder_height"           => $request["form_builder_height"],
        "edit_form_builder_link_action" => $request["edit_form_builder_link_action"]
    );
    Modules::setModuleSettings($settings);

    $success = true;
    $message = $L["notify_settings_updated"];
}

$module_settings = $module->getSettings();

$page_vars["success"] = $success;
$page_vars["message"] = $message;
$page_vars["module_settings"] = $module_settings;

$module->displayPage("templates/settings.tpl", $page_vars);
