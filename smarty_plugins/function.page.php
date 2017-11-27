<?php

use FormTools\FieldTypes;
use FormTools\FieldValidation;
use FormTools\Settings;
use FormTools\ViewFields;
use FormTools\ViewTabs;
use FormTools\Modules\FormBuilder\General;

/**
 * This template handles the page generation for the different page types: form, form offline,
 * review and thankyou. It automatically detects what page type this is and calls the appropriate
 * template.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_page($params, &$smarty)
{
    $template_vars = $smarty->getTemplateVars();
    $form_id = $template_vars["form_id"];
    $view_id = $template_vars["view_id"];
    $current_page = $template_vars["current_page"];

    // this is only set for when the form is actually in use
    $submission_id = isset($template_vars["submission_id"]) ? $template_vars["submission_id"] : "";

    // a little odd, but we renamed "template_type" to "page_type" here, because it's a little clearer.
    // We already know we're dealing with a page
    $template_info = $template_vars["templates"]["page"];
    $page_type = $template_info["template_type"];

    // form and review pages are special: they get info about the view fields
    $get_view_field_info = false;
    $validation_js = "";

    if ($page_type == "form_page") {
        // workaround for Views that didn't arrange their fields into tabs
        $tabs = ViewTabs::getViewTabs($view_id, true);
        if (empty($tabs)) {
            $current_page = "";
        }

        $grouped_fields = ViewFields::getGroupedViewFields($view_id, $current_page, $form_id, $submission_id);

        // if the user just failed server-side validation, merge in whatever info was in the POST request with what's in $grouped_fields
        if (isset($template_vars["validation_error"]) && !empty($template_vars["validation_error"])) {
            $grouped_fields = FieldValidation::mergeFormSubmission($grouped_fields, $_POST);
        }

        // get whatever validation is needed for this page
        $validation_js = FieldValidation::generateSubmissionJsValidation($grouped_fields, array(
            "form_element_id" => "ts_form_element_id",
            "custom_error_handler" => "fb_validate"
        ));

        $get_view_field_info = true;
    } else {
        if ($page_type == "review_page") {
            $grouped_fields = ViewFields::getGroupedViewFields($view_id, "", $form_id, $submission_id);
            $get_view_field_info = true;
        }
    }

    if ($get_view_field_info) {
        // remove all system fields and fields marked as non-editable
        $updated_grouped_fields = array();
        foreach ($grouped_fields as $field_info) {
            $group = $field_info["group"];
            $fields = array();
            foreach ($field_info["fields"] as $field_info) {
                if ($field_info["is_system_field"] == "yes" || $field_info["is_editable"] == "no") {
                    continue;
                }

                // a hack for a complicated scenario. The {display_custom_field} template doesn't set the settings
                // param like with the main program, so we re-use the {edit_custom_field} template and just tell it
                // that nothing is editable
                if ($page_type == "review_page") {
                    $field_info["is_editable"] = "no";
                    $field_info["submission_info"] = array();
                    $field_info["submission_info"]["value"] = isset($field_info["submission_value"]) ? $field_info["submission_value"] : "";
                }

                $fields[] = $field_info;
            }

            if (!empty($fields)) {
                $updated_grouped_fields[] = array(
                    "group" => $group,
                    "fields" => $fields
                );
            }
        }

        $field_types = FieldTypes::get(true);
        $settings = Settings::get();

        $smarty->assign("grouped_fields", $updated_grouped_fields);
        $smarty->assign("field_types", $field_types);
        $smarty->assign("settings", $settings);
    }

    if (empty($template_info["content"])) {
        $template_info["content"] = " ";
    }

    $smarty->left_delimiter = '{{';
    $smarty->right_delimiter = '}}';

    $smarty->assign("eval_str", $template_info["content"]);
    $smarty->assign("page_name", $template_vars["nav_pages"][$current_page - 1]["page_name"]);
    $smarty->assign("page_type", $page_type);

    // used in the form action attribute. This'll cause nice "/" paths for index.php to get redirected to index.php on
    // the next redirect, which is a pity
    $smarty->assign("page_url", basename($_SERVER["SCRIPT_NAME"]));

    $page = $smarty->fetch("../../modules/form_builder/smarty_plugins/eval.tpl");
    if (!empty($validation_js)) {
        $error_handler_js = General::getFormValidationCustomErrorHandlerJs();
        $page = "<script>$error_handler_js\n$validation_js</script>" . $page;
    }

    return $page;
}
