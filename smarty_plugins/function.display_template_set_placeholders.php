<?php

function smarty_function_display_template_set_placeholders($params, &$smarty)
{
  echo fb_generate_template_set_placeholders_html($params["set_id"], $params["placeholders"], $params["placeholder_hash"]);
}

