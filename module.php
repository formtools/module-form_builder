<?php

/**
 * Form Builder module.
 */

$MODULE = array();
$MODULE["author"]          = "Ben Keen";
$MODULE["author_email"]    = "ben.keen@gmail.com";
$MODULE["author_link"]     = "http://www.formtools.org";
$MODULE["version"]         = "1.0.7";
$MODULE["date"]            = "2014-05-31";
$MODULE["is_premium"]      = "no";
$MODULE["origin_language"] = "en_us";

// define the module navigation - the keys are keys defined in the language file. This lets
// the navigation - like everything else - be customized to the users language. The paths are always built
// relative to the module's root, so help/index.php means: /[form tools root]/modules/export_manager/help/index.php
$MODULE["nav"] = array(
	"phrase_template_sets" => array('{$module_dir}/index.php', false),
	"word_settings"        => array('{$module_dir}/settings.php', false),
	"word_help"            => array('{$module_dir}/help.php', false)
);