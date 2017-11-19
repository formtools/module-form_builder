<?php

function smarty_function_display_template_set_templates($params, &$smarty)
{
  echo fb_generate_template_set_templates_html($params["set_id"], $params["selected_templates"]);
}