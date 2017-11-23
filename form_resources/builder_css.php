<?php

/**
 * This is a version of the css.php that's designed for the form builder. The difference is that this
 * is passed the placeholder info directly from the builder, not pulling it from the database.
 */

use FormTools\Modules;
use FormTools\Modules\FormBuilder\General;
use FormTools\Modules\FormBuilder\Resources;

header("Content-Type: text/css");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

$g_check_ft_sessions = false;

require_once(realpath(__DIR__ . "/../../../../global/library.php"));

Modules::includeModule("form_builder");

$resource_id = $_GET["resource_id"];
$resource_info = Resources::getResource($resource_id);
$set_id = $resource_info["template_set_id"];
$css    = $resource_info["content"];

$config = array();
$smarty = General::createNewSmartyInstance();

$smarty->assign("eval_str", $css);

echo $smarty->fetch("../../modules/form_builder/smarty/eval.tpl");
