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
        echo "<div class=\"error\"><div style=\"padding: 8px\">You need to install the Form Tools API to use the {{captcha}} tag.</div></div>";
        return;
    }

    require_once(__DIR__ . "/../../../global/api/API.class.php");

    $form_namespace = $smarty->getTemplateVars("namespace");

    // if the user has already passed this CAPTCHA, add nothing to the page!
    if (isset($_SESSION[$form_namespace]["passed_captcha"])) {
        return;
    }

    $_SESSION[$form_namespace]["has_captcha"] = true;

    $api = $smarty->getTemplateVars("api");
    $api->displayCaptcha();
}
