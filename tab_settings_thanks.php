<?php

if (isset($request["update"]))
{
  $settings = array(
    "default_thankyou_page_content" => $request["default_thankyou_page_content"]
  );
  ft_set_module_settings($settings);

  $g_success = true;
  $g_message = $L["notify_settings_updated"];
}

$module_settings = ft_get_module_settings();

$page_vars["module_settings"] = $module_settings;
$page_vars["head_string"] =<<< END
<script src="{$g_root_url}/global/codemirror/js/codemirror.js"></script>
END;

ft_display_module_page("templates/settings.tpl", $page_vars);