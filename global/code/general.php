<?php


/**
 * Helper function to return a JS array of View IDs and the number of tabs in each.
 *
 * @param integer $form_id
 */
function fb_get_num_view_tabs_js($form_id)
{
	$view_ids = ft_get_view_ids($form_id);
	$view_id_to_num_tabs = array();
	foreach ($view_ids as $view_id)
	{
	  $tabs = ft_get_view_tabs($view_id, true);
	  $num_tabs = count($tabs);
	  $view_id_to_num_tabs[] = "[$view_id,$num_tabs]";
	}

	$view_id_to_num_tab_str = implode(",", $view_id_to_num_tabs);
  return $view_id_to_num_tab_str;
}


function fb_create_new_smarty_instance($delimiters = "double")
{
	global $g_root_dir, $g_smarty_use_sub_dirs;

	if ($delimiters == "single")
	{
    $left_delimiter = "{";
    $right_delimiter = "}";
	}
	else
	{
    $left_delimiter = "{{";
    $right_delimiter = "}}";
	}

  $smarty = new Smarty();
  $smarty->template_dir = "$g_root_dir/themes/default";
  $smarty->compile_dir  = "$g_root_dir/themes/default/cache/";
  $smarty->use_sub_dirs = $g_smarty_use_sub_dirs;
  $smarty->left_delimiter = $left_delimiter;
  $smarty->right_delimiter = $right_delimiter;
  $smarty->plugins_dir[] = "$g_root_dir/modules/form_builder/smarty";
  $smarty->plugins_dir[] = "$g_root_dir/global/smarty";

  return $smarty;
}


/**
 * Helper function to return a list of View tabs.
 *
 * @param array $view_info
 */
function fb_get_view_tabs_from_view_info($view_info)
{
  $view_tabs = array();
  foreach ($view_info["tabs"] as $tab_info)
  {
    $tab_label = trim($tab_info["tab_label"]);
    if (!empty($tab_label))
      $view_tabs[] = array("tab_label" => $tab_label);
  }

  if (empty($view_tabs))
  {
    $view_tabs[] = array("tab_label" => "Form"); // TODO
  }

  return $view_tabs;
}



/**
 * Creates a Form Builder form. Same as ft_create_internal_form(), except for the form type.
 *
 * @param $info the POST request containing the form name, number of fields and access type.
 */
function fb_create_form($request)
{
  global $LANG, $g_table_prefix;

  $rules = array();
  $rules[] = "required,form_name,{$LANG["validation_no_form_name"]}";
  $rules[] = "required,num_fields,{$LANG["validation_no_num_form_fields"]}";
  $rules[] = "digits_only,num_fields,{$LANG["validation_invalid_num_form_fields"]}";
  $rules[] = "required,access_type,{$LANG["validation_no_access_type"]}";

  $errors = validate_fields($request, $rules);
  if (!empty($errors))
  {
    array_walk($errors, create_function('&$el','$el = "&bull;&nbsp; " . $el;'));
    $message = join("<br />", $errors);
    return array(false, $message);
  }

  $info = ft_sanitize($request);
  $config = array(
    "form_type"    => "form_builder",
    "form_name"    => $info["form_name"],
    "access_type"  => $info["access_type"]
  );

  // set up the entry for the form
  list($success, $message, $new_form_id) = ft_setup_form($config);

  $form_data = array(
    "form_tools_form_id" => $new_form_id,
    "form_tools_display_notification_page" => false
  );

  for ($i=1; $i<=$info["num_fields"]; $i++)
  {
    $form_data["field{$i}"] = $i;
  }
  ft_initialize_form($form_data);

  $infohash = array();
  $form_fields = ft_get_form_fields($new_form_id);

  $order = 1;

  // if the user just added a form with a lot of fields (over 50), the database row size will be too
  // great. Varchar fields (which with utf-8 equates to 1220 bytes) in a table can have a combined row
  // size of 65,535 bytes, so 53 is the max. The client-side validation limits the number of fields to
  // 1000. Any more will throw an error.
  $field_size_clause = ($info["num_fields"] > 50) ? ", field_size = 'small'" : "";

  $field_name_prefix = ft_sanitize($LANG["word_field"]);
  foreach ($form_fields as $field_info)
  {
    if (preg_match("/field(\d+)/", $field_info["field_name"], $matches))
    {
      $field_id  = $field_info["field_id"];
      mysql_query("
        UPDATE {$g_table_prefix}form_fields
        SET    field_title = '$field_name_prefix $order',
              col_name = 'col_$order'
              $field_size_clause
        WHERE  field_id = $field_id
      ");
      $order++;
    }
  }

  ft_finalize_form($new_form_id);

  // if the form has an access type of "private" add whatever client accounts the user selected
  if ($info["access_type"] == "private")
  {
    $selected_client_ids = $info["selected_client_ids"];
    $queries = array();
    foreach ($selected_client_ids as $client_id)
      $queries[] = "($client_id, $new_form_id)";

    if (!empty($queries))
    {
      $insert_values = implode(",", $queries);
      mysql_query("
        INSERT INTO {$g_table_prefix}client_forms (account_id, form_id)
        VALUES $insert_values
          ");
    }
  }

  // now apply a few simple changes to the View we just created, to simplify things for the
  $views = ft_get_form_views($new_form_id);
  $view_id = $views[0]["view_id"];

  // 1. Change the View name to "Form Builder View"
  @mysql_query("UPDATE {$g_table_prefix}views SET view_name = 'Form Builder View' WHERE view_id = $view_id");

  // 2. Change the View's first tab (the only one defined!) to be called "Page 1"
  @mysql_query("UPDATE {$g_table_prefix}view_tabs SET tab_label = 'Page 1' WHERE view_id = $view_id AND tab_number = 1 LIMIT 1");

  // 3. Change the View Field Group label to "Fields" instead of "DATA"
  @mysql_query("UPDATE {$g_table_prefix}list_groups SET group_name = 'Fields' WHERE group_type = 'view_fields_{$view_id}' LIMIT 1");

  return array(true, $LANG["notify_internal_form_created"], $new_form_id);
}


/**
 * What a beautiful function name. This largely duplicates some Core JS code, but it's necessary + a good idea for later on when
 * we want to offer different ways to display validation errors.
 *
 * @return string
 */
function fb_get_form_validation_custom_error_handler_js()
{
	global $LANG;

  $js =<<< END

function fb_validate(f, error_info) {
  if (!error_info.length) {
    return true;
  }
  var first_el = null;
  var error_str = "<ul>";
  for (var i=0; i<error_info.length; i++) {
    error_str += "<li>" + error_info[i][1] + "</li>";
    if (first_el == null) {
      first_el = error_info[i][0];
    }
  }
  error_str += "</ul>";

  ft.create_dialog({
    title:      "{$LANG["phrase_validation_error"]}",
    popup_type: "error",
    width:      450,
    content:    error_str,
    buttons:    [{
      text:  "{$LANG["word_close"]}",
      click: function() {
        $(this).dialog("close");
        $(first_el).focus().select();
      }
    }]
  })

  return false;
}
END;

  return $js;
}


if (!function_exists("ft_undo_magic_quotes")) {
	function ft_undo_magic_quotes($input)
	{
		if (!get_magic_quotes_gpc())
		  return $input;

	  if (is_array($input))
	  {
	    $output = array();
	    foreach ($input as $k=>$i)
	      $output[$k] = ft_undo_magic_quotes($i);
	  }
	  else
	  {
	    $output = stripslashes($input);
	  }

	  return $output;
	}
}

