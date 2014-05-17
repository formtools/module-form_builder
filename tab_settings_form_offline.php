<?php

if (isset($request["update"]))
{
  $settings = array(
    "default_form_offline_page_content" => $request["default_form_offline_page_content"],
    "scheduled_offline_form_behaviour"  => $request["scheduled_offline_form_behaviour"]
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