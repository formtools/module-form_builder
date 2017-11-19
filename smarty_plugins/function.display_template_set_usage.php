<?php

/**
 * Used to display a list/dropdown of forms that use a template set.
 *
 * @param array $params
 * @param object $smarty
 */
function smarty_function_display_template_set_usage($params, &$smarty)
{
  global $LANG, $g_template_types, $L;

  $set_id = $params["set_id"];
  $format = $params["format"]; // dropdown / list

  $results = fb_get_template_set_usage($set_id);

  if (empty($results))
  {
    echo "<span class=\"light_grey pad_left_small\">{$L["phrase_not_used"]}</span>";
  }
  else
  {
    echo "<select>";
    while (list($form_id, $data) = each($results))
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