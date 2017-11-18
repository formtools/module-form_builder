<?php


function fb_reset_hooks()
{
  ft_unregister_module_hooks("form_builder");
  ft_register_hook("template", "form_builder", "add_form_page", "", "fb_display_add_form_option", 50, true);
  ft_register_hook("template", "form_builder", "admin_edit_form_main_tab_form_type_dropdown", "", "fb_display_form_type_option", 50, true);
  ft_register_hook("template", "form_builder", "admin_forms_form_type_label", "", "fb_display_form_builder_label", 50, true);
  ft_register_hook("code", "form_builder", "start", "ft_module_override_data", "fb_inline_data_override", 50, true);
  ft_register_hook("code", "form_builder", "end", "ft_display_custom_page_message", "fb_display_form_created_message", 50, true);
  ft_register_hook("template", "form_builder", "admin_edit_form_content", "", "fb_display_publish_tab", 50, true);
  ft_register_hook("code", "form_builder", "start", "ft_delete_form", "fb_hook_delete_form");
  ft_register_hook("code", "form_builder", "end", "ft_delete_view", "fb_hook_delete_view");
}


/**
 * This adds the "Form Builder" section on the Add Form page.
 */
function fb_display_add_form_option()
{
  global $LANG, $g_root_url;

  $L = ft_get_module_lang_file_contents("form_builder");
  $select = mb_strtoupper($LANG["word_select"]);

  echo <<< END
    <table width="100%">
      <tr>
        <td width="49%" valign="top">
          <div class="grey_box add_form_select">
            <span style="float:right">
              <input type="button" name="form_builder" class="blue bold" value="$select"
                onclick="window.location='$g_root_url/modules/form_builder/admin/add_form.php'" />
            </span>
            <div class="bold">{$L["module_name"]}</div>
            <div class="medium_grey">{$L["text_form_builder_add_form_section"]}</div>
          </div>
        </td>
        <td width="2%"> </td>
        <td width="49%"></td>
      </tr>
    </table>
END;
}


/**
 * Displays the "Form Builder" option in the Form Type dropdown on the Edit Form -> Main tab.
 */
function fb_display_form_type_option($location, $vars)
{
  $L = ft_get_module_lang_file_contents("form_builder");
  $selected = ($vars["form_info"]["form_type"] == "form_builder") ? "selected=\"selected\"" : "";
  echo "<option value=\"form_builder\" $selected>{$L["module_name"]}</option>";
}


/**
 * Used on the main Forms page, to output the label of "Form Builder". By and large, the Form Builder
 * is totally separate from the Core - despite the "form_builder" form_type ENUM option in the main forms table.
 * For elegance, I'm going to try to keep it entirely distinct, hence this module hook - instead of hardcoding
 * it in the templates.
 *
 * @param string $location
 * @param array $vars
 */
function fb_display_form_builder_label($location, $vars)
{
  $L = ft_get_module_lang_file_contents("form_builder");
  $curr_form_info = $vars["form_info"]; // the form in the current loop
  if ($curr_form_info["form_type"] == "form_builder")
  {
    echo "<span style=\"color: purple\">{$L["module_name"]}</a>";
  }
}


/**
 * This functionality was added specially for the Form Builder. It's not quite a code or template hooks, but kind of an
 * "inline code hook". A couple of key places in the code now call the ft_module_override_data() function to allow overriding
 * of any data - even info that isn't inside a function. Do a code search to see how the function works + is used.
 *
 * @param array $vars
 */
function fb_inline_data_override($vars)
{
  $module_info = ft_get_module(ft_get_module_id_from_module_folder("form_builder"));
  if ($module_info["is_installed"] != "yes" && $module_info["is_enabled"] != "yes")
    return;

  $L = ft_get_module_lang_file_contents("form_builder");
  switch ($vars["location"])
  {
    // this adds the "Publish" tab to the Edit Form pages
    case "admin_edit_form_tabs":
      $tabs = $vars["data"];
      $tabs["publish"] = array(
        "tab_label" => $L["word_publish"],
        "tab_link"  => "?page=publish",
        "pages"     => array("publish")
      );
      return array("data" => $tabs);
      break;

    // this ensures the right code page is called when the user clicks on the Publish tab
    case "admin_edit_form_page_name_include":
    	$request = array_merge($_POST, $_GET);
    	if (isset($request["page"]) && $request["page"] == "publish")
    	{
      	$file = realpath(dirname(__FILE__) . "/../../admin/tab_publish.php");
      	return array("data" => array("page_name" => $file));
    	}
      break;
  }
}


/**
 * Used to render the Publish tab on the Edit Form pages.
 *
 * @param string $location
 * @param array $vars
 */
function fb_display_publish_tab($location, $vars)
{
	global $g_root_dir, $LANG, $g_form_builder_demo_mode;

	$form_id = $vars["form_info"]["form_id"];
	$L = ft_get_module_lang_file_contents("form_builder");

	$published_forms = fb_get_published_forms($form_id);

	// loop through each published form and take any offline that need it
	$at_least_one_form_just_taken_offline = false; // yes, this variable name ROCKS!!!!!
  foreach ($published_forms["results"] as $config)
  {
  	if ($config["is_online"] == "yes" && $config["offline_date"] != "0000-00-00 00:00:00")
  	{
      $taken_offline = fb_take_scheduled_form_offline($config);
      if ($taken_offline)
      {
      	$at_least_one_form_just_taken_offline = true;
      }
  	}
  }

  // if one of the forms was just taken offline, re-request the published form list so they'll show as online = "no" in the UI
  // on this page load
  if ($at_least_one_form_just_taken_offline)
  {
    $published_forms = fb_get_published_forms($form_id);
  }

  $demo_mode = ft_get_module_settings("demo_mode", "form_builder");

  $form_type = ucwords($vars["form_info"]["form_type"]);
  $text_non_form_builder_form = ft_eval_smarty_string($L["text_non_form_builder_form"], array("form_type" => $form_type));

  $smarty = fb_create_new_smarty_instance("single");
  $smarty->assign("L", $L);
  $smarty->assign("LANG", $LANG);
  $smarty->assign("form_id", $form_id);
  $smarty->assign("form_info", $vars["form_info"]);
  $smarty->assign("published_forms", $published_forms);
  $smarty->assign("demo_mode", $demo_mode);
  $smarty->assign("text_non_form_builder_form", $text_non_form_builder_form);

  $output = $smarty->fetch("$g_root_dir/modules/form_builder/templates/admin/tab_publish.tpl");
  echo $output;
}


/**
 * Called after the user creates a new Form Builder form. It returns a custom message to display in the page.
 *
 * @param unknown_type $info
 */
function fb_display_form_created_message($info)
{
	$flag = $info["flag"];
	if ($flag != "notify_form_builder_form_created")
	{
		return;
	}

	$L = ft_get_module_lang_file_contents("form_builder");
	$message =<<< END
{$L["notify_form_builder_form_created"]}

<ul style="margin-bottom: 0px">
  <li><a href="http://modules.formtools.org/form_builder/index.php?page=tutorials" target="_blank">{$L["phrase_quick_intro"]}</a></li>
  <li><a href="http://modules.formtools.org/form_builder/index.php?page=index" target="_blank">{$L["phrase_form_builder_doc"]}</a></li>
</ul>
END;

	return array(
	  "g_success" => true,
	  "g_message" => $message
	);
}


/**
 * This deletes all form configurations and published forms when a form is deleted.
 *
 * @param array $info
 */
function fb_hook_delete_form($info)
{
	$form_id = $info["form_id"];
	$published_forms = fb_get_published_forms($form_id);
	foreach ($published_forms["results"] as $config)
	{
		$published_form_id = $config["published_form_id"];
		list($success, $message) = fb_delete_published_form($form_id, $published_form_id, "yes");

		// if there was a problem with the last function call, there was probably just a problem deleting
		// one of the files. Ignore this: just re-call the function with override "on". This ensures the configuration
		// is at least deleted
		if (!$success)
		  fb_delete_published_form($form_id, $published_form_id, "yes", true);
	}
}


/**
 * This is called whenever the administrator deletes a View. It checks to see if the View is being used for a published form.
 * If it IS, it deletes that published form + configuration.
 *
 * @param array $info
 */
function fb_hook_delete_view($info)
{
	global $g_table_prefix;

	if (!isset($info["view_id"]) || !is_numeric($info["view_id"]))
	  return;

  $view_id = $info["view_id"];

  $query = mysql_query("
    SELECT published_form_id, form_id
    FROM   {$g_table_prefix}module_form_builder_forms
    WHERE  view_id = $view_id
  ");

  while ($row = mysql_fetch_assoc($query))
  {
    $published_form_id = $row["published_form_id"];
    $form_id           = $row["form_id"];

    // always attempt to delete the published form as well as the config first. If that fails, just delete the configuration
    list($success, $message) = fb_delete_published_form($form_id, $published_form_id, "yes");
    if (!$success) {
    	fb_delete_published_form($form_id, $published_form_id, "yes", true);
    }
  }
}


