<?php

function smarty_function_display_placeholder_field_type($params, &$smarty)
{
	global $LANG;

  $type = $params["type"];

  switch ($type)
  {
  	case "textbox":
  		echo $LANG["word_textbox"];
  		break;
    case "textarea":
      echo $LANG["word_textarea"];
      break;
    case "password":
      echo $LANG["word_password"];
      break;
    case "radios":
      echo $LANG["phrase_radio_buttons"];
      break;
    case "checkboxes":
      echo $LANG["word_checkboxes"];
      break;
    case "select":
      echo $LANG["word_dropdown"];
      break;
    case "multi-select":
      echo $LANG["phrase_multi_select_dropdown"];
      break;
  }
}