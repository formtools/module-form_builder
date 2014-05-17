<?php

/**
 * A helper function for users. Works just like in_array, except it does an isset() test as well,
 * plus I think "in" is a little simpler to understand.
 *
 * Usage:
 *
 *   {{if "one"|in:$P.placeholder_name}}...{{/if}}
 *
 * @param mixed $needle
 * @param mixed $haystack
 */
function smarty_modifier_in($needle, $haystack)
{
	if (!isset($haystack) || !is_array($haystack))
	  return false;

  return in_array($needle, $haystack);
}
