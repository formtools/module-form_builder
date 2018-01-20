<?php

use FormTools\Core;
use FormTools\Modules;

/**
 * This is a convenience function that can be added to any template set. It outputs an "EDIT IN FORM BUILDER" link
 * in the page (location depends on the Template Set, but usually top-right), which takes the user to the Edit Form -> Publish
 * tab within Form Tools and opens up the Form Builder window.
 *
 * Note: the link only ever shows up if the user is already logged in as an administrator.
 *
 * @param array $params
 * @param array $smarty
 */
function smarty_function_form_builder_edit_link($params, &$smarty)
{
    if (!Core::$user->isAdmin()) {
        return;
    }

    if ($smarty->getTemplateVars("mode") != "live") {
        return;
    }
    $root_url = Core::getRootUrl();

    $published_form_id = $smarty->getTemplateVars("published_form_id");
    $form_id = $smarty->getTemplateVars("form_id");

    $action = Modules::getModuleSettings("edit_form_builder_link_action", "form_builder");
    $target = ($action == "new_window") ? "target=\"_blank\"" : "";

    echo "<a href=\"$root_url/admin/forms/edit/?form_id=$form_id&published_form_id=$published_form_id&page=publish&action=auto_open\" id=\"form_builder__edit_link\" $target>EDIT IN FORM BUILDER</a>";
}
