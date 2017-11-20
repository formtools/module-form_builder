<?php

require_once("../../../global/library.php");

use FormTools\Core;
use FormTools\General;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\TemplateSets;

$module = Modules::initModulePage("admin");
$L = $module->getLangStrings();
$LANG = Core::$L;

$set_id = Modules::loadModuleField("form_builder", "set_id", "set_id");
$page = Modules::loadModuleField("form_builder", "page", "edit_template_set", "templates");

$same_page = General::getCleanPhpSelf();
$tabs = array(
    "info" => array(
        "tab_label" => $L["word_info"],
        "tab_link" => "{$same_page}?page=info&set_id={$set_id}",
        "pages" => array("info")
    ),
    "templates" => array(
        "tab_label" => $L["word_templates"],
        "tab_link" => "{$same_page}?page=templates&set_id={$set_id}",
        "pages" => array("templates", "edit_template")
    ),
    "resources" => array(
        "tab_label" => $L["word_resources"],
        "tab_link" => "{$same_page}?page=resources&set_id={$set_id}",
        "pages" => array("resources", "edit_resource")
    ),
    "placeholders" => array(
        "tab_label" => $L["word_placeholders"],
        "tab_link" => "{$same_page}?page=placeholders&set_id={$set_id}",
        "pages" => array("placeholders", "add_placeholder", "edit_placeholder")
    )
);


$links = TemplateSets::getTemplateSetPrevNextLinks($set_id);
$prev_tabset_link = (!empty($links["prev_set_id"])) ? "index.php?page=$page&set_id={$links["prev_set_id"]}" : "";
$next_tabset_link = (!empty($links["next_set_id"])) ? "index.php?page=$page&set_id={$links["next_set_id"]}" : "";

// start compiling the page vars here (save duplicate code!)
$page_vars = array(
    "set_id" => $set_id,
    "page" => $page,
    "tabs" => $tabs,
    "show_tabset_nav_links" => true,
    "prev_tabset_link" => $prev_tabset_link,
    "next_tabset_link" => $next_tabset_link,
    "prev_tabset_link_label" => $L["phrase_prev_template_set"],
    "next_tabset_link_label" => $L["phrase_next_template_set"]
);

switch ($page) {
    case "info":
        include("tab_info.php");
        break;
    case "templates":
        include("tab_templates.php");
        break;
    case "edit_template":
        include("tab_edit_template.php");
        break;
    case "resources":
        include("tab_resources.php");
        break;
    case "edit_resource":
        include("tab_edit_resource.php");
        break;
    case "placeholders":
        include("tab_placeholders.php");
        break;
    case "add_placeholder":
        include("tab_add_placeholder.php");
        break;
    case "edit_placeholder":
        include("tab_edit_placeholder.php");
        break;

    default:
        include("tab_info.php");
        break;
}
