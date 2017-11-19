<?php

function smarty_function_template_type_dropdown($params, &$smarty)
{
  global $LANG, $g_template_types;

  $name_id       = $params["name_id"];
  $set_id        = $params["set_id"];
  $template_type = $params["type"];
  $default = isset($params["default"]) ? $params["default"] : "";
  $class   = isset($params["class"]) ? $params["class"] : "";
  $single_item_class = isset($params["single_item_class"]) ? $params["single_item_class"] : "light_grey pad_left_small";

  $headers = fb_get_template_type($set_id, $template_type);

  if (empty($headers))
  {
    echo "None exist.";
  }
  else if (count($headers) == 1)
  {
  	echo "<span class=\"$single_item_class\">{$headers[0]["template_name"]}</span>";
  }
  else
  {
    echo "<select name=\"$name_id\" id=\"$name_id\" class=\"$class\">";
    foreach ($headers as $header_info)
    {
      $selected = ($header_info["template_id"] == $default) ? "selected" : "";
      echo "<option value=\"{$header_info["template_id"]}\" $selected>{$header_info["template_name"]}</option>";
    }
    echo "</select>";
  }
}