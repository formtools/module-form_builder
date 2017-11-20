<?php

require_once("../../global/library.php");

use FormTools\Core;
use FormTools\General;
use FormTools\Modules;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$LANG = Core::$L;

$page = Modules::loadModuleField("form_builder", "page", "tab", "main");
$php_self = General::getCleanPhpSelf();

$tabs = array(
    "main" => array(
        "tab_label" => $LANG["word_main"],
        "tab_link" => "$php_self?page=main"
    ),
    "thanks" => array(
        "tab_label" => $L["phrase_thankyou_page"],
        "tab_link" => "$php_self?page=thanks"
    ),
    "form_offline" => array(
        "tab_label" => $L["phrase_offline_forms"],
        "tab_link" => "$php_self?page=form_offline"
    )
);

$page_vars = array(
    "page" => $page,
    "tabs" => $tabs,
    "allow_url_fopen" => ini_get("allow_url_fopen") == "1"
);

// load the appropriate code pages
switch ($page) {
    case "main":
        require("tab_settings_main.php");
        break;
    case "thanks":
        require("tab_settings_thanks.php");
        break;
    case "form_offline":
        require("tab_settings_form_offline.php");
        break;
    default:
        require("tab_settings_main.php");
        break;
}
