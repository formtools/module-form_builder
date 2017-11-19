<?php

function smarty_function_display_template_type($params, &$smarty)
{
	global $LANG, $g_template_types, $L;

  $type = $params["type"];

  while (list($group_name, $vals) = each($g_template_types))
  {
  	$found = false;
  	while (list($section_key, $section_name) = each($vals))
  	{
  	  if ($section_key == $type)
  	  {
  	  	$section_name = ft_eval_smarty_string("{\$" . $section_name . "}", $L);
  	  	echo "<span class=\"set_type_$section_key\">$section_name</span>";
  	  	$found = true;
  	  	break;
  	  }
  	}
  	if ($found)
  	  break;
  }
  reset($g_template_types);
}