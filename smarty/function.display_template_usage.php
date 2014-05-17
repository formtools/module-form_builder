<?php

/**
 * Used to display a list/dropdown of forms that use a template.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_display_template_usage($params, &$smarty)
{
  global $LANG, $g_template_types, $L;

  $usage = $params["usage"];

  if (empty($usage))
  {
    echo "<span class=\"light_grey pad_left_small\">{$L["phrase_not_used"]}</span>";
  }
  else
  {
    echo "<select>";
    while (list($form_id, $data) = each($usage))
    {
    	$form_name = htmlspecialchars($data["form_name"]);
    	$usage     = $data["usage"];
    	echo "<optgroup label=\"$form_name\"></optgroup>";
      foreach ($usage as $i)
      {
      	echo "<option>{$i["full_url"]}</option>";
      }
      echo "</optgroup>";
    }
    echo "</select>";
  }
}