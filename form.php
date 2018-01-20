<?php

use FormTools\Core;
use FormTools\General as CoreGeneral;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\Forms;
use FormTools\Modules\FormBuilder\FormGenerator;
use FormTools\ViewTabs;


if (Core::isAPIAvailable()) {
    require_once(__DIR__ . "/../../global/api/API.class.php");
    $api = new FormTools\API(array("init_core" => false));
}

/**
 * If the user uninstalls the Form Builder or disables it, any public forms will stop working. This is called at the
 * top of the forms to output a simple "Offline" message. It's really just to prevent an ugly error.
 *
 * Note: this is DIFFERENT from the form offline message which is configured per form.
 */
if (!Modules::checkModuleUsable("form_builder")) {
    echo <<< END
<!DOCTYPE html>
<html>
<head></head>
<body>
    <p>
        This form is unavailable. 
    </p>
</body>
</html>
END;
    exit;
}

$module = Modules::getModuleInstance("form_builder");
$namespace = "form_builder_{$published_form_id}";

// find out about the page: form / review / thanks. That determines what values we pass to processFormBuilderPage
$config = Forms::getFormConfiguration($published_form_id);

$form_id = $config["form_id"];
$view_id = $config["view_id"];

FormGenerator::deleteUnfinalizedSubmissions($form_id);

// check that we have all the info we need (configured form, View etc)
$error_code = FormGenerator::checkLiveFormConditions($config);
if (!empty($error_code)) {
    $config["error_code"] = $error_code;
    FormGenerator::generateFormPage($config);
    exit;
}

if (isset($_GET["clear"])) {
    FormGenerator::clearFormBuilderFormSessions($namespace);
    header("location: $filename");
}

// check the form shouldn't be taken offline. This does some special logic to override the is_online == "no"
// for cases where submissions have been started but the form is now offline
$is_online = FormGenerator::checkFormOffline($config, $namespace);
$config["is_online"] = ($is_online) ? "yes" : "no";

// set up sessions and retrieve the field data already submitted
list($new_session, $fields) = FormGenerator::initFormBuilderPage($form_id, $view_id, $namespace);

// get the current submission ID
$submission_id = isset($_SESSION[$namespace]["form_tools_submission_id"]) ? $_SESSION[$namespace]["form_tools_submission_id"] : "";

// get an ordered list of the pages in this published form
$page_params = array(
    "view_tabs" => ViewTabs::getViewTabs($view_id, true),
    "include_review_page" => ($config["include_review_page"] == "yes") ? true : false,
    "include_thanks_page_in_nav" => ($config["include_thanks_page_in_nav"] == "yes") ? true : false,
    "review_page_title" => $config["review_page_title"],
    "thankyou_page_title" => $config["thankyou_page_title"]
);

$all_pages = Forms::getAllFormPages($page_params);
$page = CoreGeneral::loadField("page", "{$namespace}_form_page", 1, $namespace); // TODO...

// one additional check: make sure the page they're attempting to look at is permitted. They have to pass
// through each page in order to prevent people bypassing any field validation
$page = FormGenerator::verifyPageNumber($page, $all_pages, $namespace);

$next_page = $page + 1;
$page_info = $all_pages[$page - 1];
$page_type = $page_info["page_type"];


$post_values = array();
$params = array();
if ($page_type == "thanks") {
    FormGenerator::clearFormBuilderFormSessions($namespace);
} else {
    $params = array(
        "namespace" => $namespace,
        "published_form_id" => $published_form_id,
        "submission_id" => $submission_id,
        "config" => $config,
        "page" => $page,
        "page_type" => $page_type,
        "form_id" => $form_id,
        "view_id" => $view_id,
        "submit_button" => "form_tools_continue",
        "next_page" => "$filename?page=$next_page",
        "form_data" => $_POST,
        "file_data" => $_FILES,
        "no_sessions_url" => "$filename?page=1"
    );

    // we need to finalize the submission on the penultimate page. This can mean either the Review page or
    // the final form page if there's no Review page
    $num_pages = count($all_pages);
    if ($page_type == "review" || ($page >= $num_pages - 1)) {
        $params["finalize"] = true;
    }

    // just in case
    if ($page == $num_pages && isset($params["form_data"]["form_tools_continue"])) {
        $params["next_page"] = "";
    }

    list($g_success, $g_message) = FormGenerator::processFormBuilderPage($params);

    // if there were any validation errors, pass the error along to the page. It'll use it to know not to
    // redirecting and to show the error message
    if (!$g_success) {
        $config["validation_error"] = $g_message;
    }
}

// now generate and display the form
FormGenerator::generateFormPage($config, $page, $submission_id);
