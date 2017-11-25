<?php

/**
 * This is a version of the css.php that's designed for the form builder. The difference is that this
 * is passed the placeholder info directly from the builder, not pulling it from the database.
 *
 * TODO THIS FILE IS A MESS. THINK IT THROUGH AGAIN.
 */

use FormTools\Core;
use FormTools\DatabaseSessions;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\Forms;
use FormTools\Modules\FormBuilder\General;
use FormTools\Modules\FormBuilder\Placeholders;
use FormTools\Modules\FormBuilder\Resources;

require_once(realpath(__DIR__ . "/../../../global/library.php"));

Core::init(array("start_sessions" => false));
Modules::includeModule("form_builder");

$resource_id = $_GET["resource_id"];
$source = (isset($_GET["source"]) && $_GET["source"] == "sessions") ? "sessions" : "database";

$resource_info = Resources::getResource($resource_id);
$set_id = $resource_info["template_set_id"];
$css = $resource_info["content"];

$placeholders = Placeholders::getPlaceholders($set_id);
$placeholder_hash = array();
foreach ($placeholders as $placeholder_info) {
    $placeholder_hash[$placeholder_info["placeholder_id"]] = $placeholder_info["placeholder"];
}

$config = array();
$smarty = General::createNewSmartyInstance();

$P = array();
if ($source == "sessions") {
    if (Core::getSessionType() == "database") {
        $sess = new DatabaseSessions(Core::$db, Core::getSessionSavePath());
    }
    if (!empty($g_session_save_path)) {
        session_save_path($g_session_save_path);
    }

    session_start();
    header("Cache-control: private");
    header("Content-Type: text/html; charset=utf-8");

    $placeholder_id_to_values = $_SESSION["ft"]["form_builder"]["placeholders"];

    while (list($placeholder_id, $value) = each($placeholder_id_to_values)) {
        if (!isset($placeholder_hash[$placeholder_id])) {
            continue;
        }
        $placeholder = $placeholder_hash[$placeholder_id];

        // TODO multi-select + checkboxes...
        $P[$placeholder] = $value;
    }
} else {
    $config = Forms::getFormConfiguration($_GET["published_form_id"]);
    foreach ($config["placeholders"] as $placeholder_info) {
        $curr_placeholder_id = $placeholder_info["placeholder_id"];
        $val = $placeholder_info["placeholder_value"];

        if (!isset($placeholder_hash[$curr_placeholder_id])) {
            continue;
        }

        $placeholder = $placeholder_hash[$curr_placeholder_id];
        $P[$placeholder] = $val;
    }
}

$smarty->assign("P", $P);
$smarty->assign("eval_str", $css);

header("Content-Type: text/css");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

echo $smarty->fetch("../../modules/form_builder/smarty_plugins/eval.tpl");
