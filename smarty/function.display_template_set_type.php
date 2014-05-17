<?php

// yeesh... terminology for "sections" / "types" / "names" is terrible... they all refer to the same thing
function smarty_function_display_template_set_type($params, &$smarty)
{
	global $LANG, $g_template_types;

  $type = $params["type"];

  while (list($group_name, $vals) = each($g_template_types))
  {
  	$found = false;
  	while (list($section_key, $section_name) = each($vals))
  	{
  	  if ($section_key == $type)
  	  {
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