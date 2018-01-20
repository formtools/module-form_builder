<?php

use FormTools\Core;

/**
 * Includes a reCAPTCHA in the page. It only includes it ONCE. If the user fills it in successfully,
 * they aren't bothered again when returning to the same page.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_captcha($params, &$smarty)
{
    if (!Core::isAPIAvailable()) {
        echo "API not available.";
        exit;
    }

    require_once(__DIR__ . "/../../../global/api/API.class.php");

    $form_namespace = $smarty->getTemplateVars("namespace");

    // if the user has already passed this CAPTCHA, add nothing to the page!
    if (isset($_SESSION[$form_namespace]["passed_captcha"])) {
        return;
    }

    $_SESSION[$form_namespace]["has_captcha"] = true;

    $api = $smarty->getTemplateVars("api");
    $response = $api->displayCaptcha();

//    $error_message = "";
//    if (is_array($response) && $response[0] === false) {
//        $error_message = "You need to define the reCAPTCHA public and private keys.";
//    }
//
//    if (!empty($error_message)) {
//        echo "<div class=\"ft_error\">$error_message</div>";
//    }
}
