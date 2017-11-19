<?php

/**
 * Includes a reCAPTCHA in the page. It only includes it ONCE. If the user fills it in successfully,
 * they aren't bothered again when returning to the same page.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_captcha($params, &$smarty)
{
  include_once(realpath(dirname(__FILE__) . "/../../../global/api/api.php"));

  $form_namespace = $smarty->_tpl_vars["namespace"];

  // if the user has already passed this CAPTCHA, add nothing to the page!
  if (isset($_SESSION[$form_namespace]["passed_captcha"]))
  {
  	return;
  }

  $error_message = "";
  if (function_exists("ft_api_display_captcha"))
  {
  	$GLOBALS["g_api_debug"] = false;
  	$response = ft_api_display_captcha();

  	if (is_array($response) && $response[0] === false)
  	{
      $error_message = "You need to define the reCAPTCHA public and private keys.";
  	}
  }
  else
  {
  	$error_message = "The Form Tools API must be installed in order to add a reCAPTCHA to your form.";
  }

  if (!empty($error_message))
  {
    echo "<div class=\"ft_error\">$error_message</div>";
  }
}
