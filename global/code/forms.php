<?php


/**
 * Returns information about a form configuration.
 *
 * @param $published_form_id
 */
function fb_get_form_configuration($published_form_id)
{
	global $g_table_prefix;

	if (empty($published_form_id) || !is_numeric($published_form_id))
	  return array();

	$query = mysql_query("SELECT * FROM {$g_table_prefix}module_form_builder_forms WHERE published_form_id = $published_form_id");
	$result = mysql_fetch_assoc($query);

	if (empty($result))
	  return array();

  $templates_query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_form_templates
    WHERE  published_form_id = $published_form_id
  ");
  $templates = array();
  while ($row = mysql_fetch_assoc($templates_query))
  {
    $templates[] = $row;
  }
  $result["templates"] = $templates;

  // 3. Placeholders
  $placeholders_query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_form_placeholders
    WHERE  published_form_id = $published_form_id
  ");
  $placeholders = array();
  while ($row = mysql_fetch_assoc($placeholders_query))
  {
    $placeholders[] = $row;
  }
  $result["placeholders"] = $placeholders;

	return $result;
}


/**
 * Returns all published versions of a form.
 *
 * @param unknown_type $form_id
 */
function fb_get_published_forms($form_id)
{
  global $g_table_prefix;

  $search_results = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_forms
    WHERE  form_id = $form_id
    ORDER BY list_order
      ");

  $configured_forms = array();
  while ($row = mysql_fetch_assoc($search_results))
  {
  	$configured_forms[] = $row;
  }

  $count_results = mysql_query("
    SELECT count(*) as c
    FROM   {$g_table_prefix}module_form_builder_forms
      ");
  $count_hash = mysql_fetch_assoc($count_results);

  $return_hash["results"]     = $configured_forms;
  $return_hash["num_results"] = $count_hash["c"];

  return $return_hash;
}



function fb_update_configured_form_templates($id, $info)
{
  global $g_table_prefix, $L;

  $set_id = $info["template_set_id"];
	mysql_query("UPDATE {$g_table_prefix}module_form_builder_form_templates SET set_id = $set_id WHERE published_form_id = $id");

  mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_templates WHERE published_form_id = $id");
	$template_data = array(
	  "page_layout"    => $info["page_layout_template_id"],
    "header"         => $info["header_template_id"],
	  "footer"         => $info["footer_template_id"],
	  "navigation"     => $info["navigation_template_id"],
	  "form_page"      => $info["form_page_template_id"],
	  "review_page"    => $info["review_page_template_id"],
	  "thankyou_page"  => $info["thankyou_page_template_id"],
	  "continue_block" => $info["continue_block_template_id"],
	  "error_message"  => $info["error_message_template_id"]
	);

  while (list($key, $template_id) = each($template_data))
  {
    mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_form_templates (published_form_id, template_type, template_id)
      VALUES ($id, '$key', $template_id)
    ") or die(mysql_error());
  }

  return array(true, $L["notify_template_set_templates_updated"]);
}



function fb_delete_form_configuration($form_id, $id)
{
	global $g_table_prefix, $L;

	mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_forms WHERE published_form_id = $id");
	mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_pages WHERE published_form_id = $id");
	mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_placeholders WHERE published_form_id = $id");
	mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_templates WHERE published_form_id = $id");

	fb_update_published_form_order($form_id);

  return array(true, $L["notify_form_configuration_deleted"]);
}


/**
 * Deletes a published form and, optionally, the form configuration as well.
 *
 * @param integer $form_id
 * @param integer $published_form_id
 * @param string $delete_form_config "yes" / "no"
 * @param boolean $override
 */
function fb_delete_published_form($form_id, $published_form_id, $delete_form_config, $override = false)
{
	global $g_table_prefix, $L;

  $config = fb_get_form_configuration($published_form_id);
  if (empty($config))
  {
  	return array(false, $L["notify_delete_form_config_not_found"]);
  }

  $folder_path = $config["folder_path"];
  $filename    = $config["filename"];

  // see if the file exists (only bother if the user isn't overriding)
  if (!$override)
  {
    $file = "$folder_path/$filename";
    if (!is_file($file))
    {
  	  $ignore_link = "edit.php?page=publish&delete_published_form=$published_form_id&delete_form_config=$delete_form_config&override=1";
  	  $params = array(
  	    "file"        => $file,
  	    "ignore_link" => $ignore_link
  	  );
  	  $message = ft_eval_smarty_string($L["notify_form_missing_cannot_delete"], $params);
      return array(false, $message);
    }

    // this probably isn't necessary, but it doesn't hurt: change the permissions on the file to 777
    @chmod($file, 0777);
    if (!@unlink($file))
    {
    	array(false, $L["notify_cannot_delete_form_file"]);
    }
  }

  if ($delete_form_config == "yes")
  {
  	fb_delete_form_configuration($form_id, $published_form_id);
  }
  else
  {
  	// update the form configuration to make a note of the fact that it's no longer published
  	mysql_query("
  	  UPDATE {$g_table_prefix}module_form_builder_forms
  	  SET    is_published = 'no',
             publish_date = NULL,
  	         filename = '',
  	         folder_path = '',
  	         folder_url = ''
  	  WHERE  published_form_id = $published_form_id
  	");
  }

  return array(true, $L["notify_published_form_deleted"]);
}


/**
 * Helper function to build a list of pages in the form. This is used for generating the list of visible
 * pages in the Form Builder, and in the actual generated forms.
 *
 * @param $view_id
 * @param $include_review_page
 * @param return an array of hashes; each hash contains information about the page. It has the following keys:
 *    page_name (the tab name)
 *    page_type (form, review, thanks)
 */
function fb_get_nav_pages($params)
{
	$view_tabs                  = $params["view_tabs"];
	$include_review_page        = $params["include_review_page"];
	$include_thanks_page_in_nav = $params["include_thanks_page_in_nav"];
  $review_page_title          = $params["review_page_title"];
  $thankyou_page_title        = $params["thankyou_page_title"];

  $pages = array();
  if (!empty($view_tabs))
  {
  	$count = 1;
  	foreach ($view_tabs as $tab_label)
  	{
  	  $pages[] = array(
        "page_name" => $tab_label["tab_label"],
  	    "page_type" => "form"
  	  );
  	  $count++;
  	}
  }
  else
  {
    $pages[] = array(
      "page_name" => "Form",
      "page_type" => "form"
    );
  }

  if ($include_review_page)
  {
    $pages[] = array(
      "page_name" => $review_page_title,
      "page_type" => "review"
    );
  }

  if ($include_thanks_page_in_nav)
  {
    $pages[] = array(
      "page_name" => $thankyou_page_title,
      "page_type" => "thanks"
    );
  }

  return $pages;
}



/**
 * A sister function to fb_get_nav_pages() above. This returns all pages in the form, including the review +
 * thankyou page - the Review page isn't included if need be, but the Thankyou page always is.
 *
 * @param array $params
 * @return array $pages
 */
function fb_get_all_form_pages($params)
{
  $view_tabs           = $params["view_tabs"];
  $include_review_page = $params["include_review_page"];
  $review_page_title   = $params["review_page_title"];
  $thankyou_page_title = $params["thankyou_page_title"];

  $pages = array();
  if (!empty($view_tabs))
  {
    $count = 1;
    foreach ($view_tabs as $tab_label)
    {
      $pages[] = array(
        "page_name" => $tab_label["tab_label"],
        "page_type" => "form"
      );
      $count++;
    }
  }
  else
  {
    $pages[] = array(
      "page_name" => "Form",
      "page_type" => "form"
    );
  }

  if ($include_review_page)
  {
    $pages[] = array(
      "page_name" => $review_page_title,
      "page_type" => "review"
    );
  }

  $pages[] = array(
    "page_name" => $thankyou_page_title,
    "page_type" => "thanks"
  );

  return $pages;
}


/**
 * This figures out the page type (form, review, thanks) for an online form. If the page number being
 * passed isn't valid, it returns false.
 *
 * @return mixed "form_page", "review_page", "thanks_page" or false if the page number is invalid
 */
function fb_get_current_page_type($page_num, $view_tabs, $include_review_page)
{
  $total_num_pages = count($view_tabs) + 1;
  if ($include_review_page)
    $total_num_pages++;

  if (!is_numeric($page_num) || $page_num < 1 || $page_num > $total_num_pages)
    return false;

  $page_type = "form_page";
  if ($page_num == $total_num_pages)
    $page_type = "thanks_page";
  else if ($include_review_page && $page_num == $total_num_pages-1)
    $page_type = "review_page";

  return $page_type;
}


/**
 * This actually creates the form file.
 */
function fb_publish_form($info)
{
	global $g_root_dir, $g_table_prefix, $L;

	$published_form_id = $info["published_form_id"];
	$filename          = $info["publish_filename"] . ".php";
	$folder_path       = $info["publish_folder_path"];
	$folder_url        = $info["publish_folder_url"];

  if (!is_dir($folder_path))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_folder_path_not_folder"]
  	);
  }

  if (!is_writable($folder_path))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_folder_path_not_writable"]
  	);
  }

  // don't allow overwriting of existing files
  if (is_file("$folder_path/$filename"))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_file_already_exists"]
  	);
  }

  $folder_path = preg_replace("/\/$/", "", $folder_path);
  $folder_url  = preg_replace("/\/$/", "", $folder_url);

  $content = fb_get_generated_form_content($published_form_id, $filename);
  $file = $folder_path . "/" . $filename;
	if ($fh = fopen($file, 'w'))
	{
  	fwrite($fh, $content);
	  fclose($fh);
	  $publish_date = ft_get_current_datetime();
	  $url = $folder_url . "/" . $filename;

	  $query = mysql_query("
	    UPDATE {$g_table_prefix}module_form_builder_forms
	    SET    is_published = 'yes',
	           publish_date = '$publish_date',
	           filename = '$filename',
	           folder_path = '$folder_path',
	           folder_url = '$folder_url'
	    WHERE  published_form_id = $published_form_id
	  ");

	  return array(
	    "success"     => true,
	    "url"         => $url,
	    "filename"    => $filename,
	    "folder_path" => $folder_path,
	    "folder_url"  => $folder_url
	  );
	}
  else
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_general_error_creating_form"]
    );
  }
}


function fb_update_publish_settings($info)
{
	global $g_root_dir, $g_table_prefix, $L;

	$published_form_id = $info["published_form_id"];
	$new_filename      = trim($info["new_publish_filename"]) . ".php";
	$old_filename      = trim($info["old_publish_filename"]) . ".php";
	$new_folder_path   = trim($info["new_publish_folder_path"]);
	$old_folder_path   = trim($info["old_publish_folder_path"]);
	$new_folder_url    = trim($info["new_publish_folder_url"]);
	$old_folder_url    = trim($info["old_publish_folder_url"]);

	// first, verify the new folder + file settings
  if (!is_dir($new_folder_path))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_folder_path_not_folder"]
  	);
  }

  if (!is_writable($new_folder_path))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_folder_path_not_writable"]
  	);
  }

  // don't allow overwriting of existing files
  if (is_file("$new_folder_path/$new_filename"))
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_file_already_exists"]
  	);
  }

  // all's good! Now delete the older file. If it can't be done (for ANY reason), we inform the user and give them
  // the option just to ignore the problem (which is indicated by "override" being passed as true)
  if ($info["override"] == "false")
  {
    $params = array(
      "old_file" => "$old_folder_path/$old_filename",
      "onclick"  => "return builder_js.overide_publish_settings()"
    );
    $notify_previous_file_not_exist = ft_eval_smarty_string($L["notify_previous_file_not_exist"], $params);
    if (!is_file("$old_folder_path/$old_filename"))
	  {
	  	return array(
	  	  "success" => false,
	  	  "message" => $notify_previous_file_not_exist
	  	);
	  }
	  else
	  {
      $result = @unlink("$old_folder_path/$old_filename");
      if (!$result)
      {
	  	  return array(
	  	    "success" => false,
	  	    "message" => $notify_previous_file_not_exist
	  	  );
      }
	  }
  }

  $new_folder_path = preg_replace("/\/$/", "", $new_folder_path);
  $new_folder_url  = preg_replace("/\/$/", "", $new_folder_url);
  $content = fb_get_generated_form_content($published_form_id, $new_filename);
  $file = $new_folder_path . "/" . $new_filename;
	if ($fh = fopen($file, 'w'))
	{
  	fwrite($fh, $content);
	  fclose($fh);
	  $publish_date = ft_get_current_datetime();
	  $url = $new_folder_url . "/" . $new_filename;

	  $query = mysql_query("
	    UPDATE {$g_table_prefix}module_form_builder_forms
	    SET    is_published = 'yes',
	           publish_date = '$publish_date',
	           filename = '$new_filename',
	           folder_path = '$new_folder_path',
	           folder_url = '$new_folder_url'
	    WHERE  published_form_id = $published_form_id
	  ");

	  return array(
	    "success"     => true,
	    "url"         => $url
	  );
	}
  else
  {
  	return array(
  	  "success" => false,
  	  "message" => $L["notify_general_error_creating_form"]
    );
  }
}


/**
 * Updates the values entered into placeholders for a form.
 *
 * @param $published_form_id
 * @param $info
 */
function fb_update_form_placeholders($published_form_id, $info)
{
  global $g_table_prefix, $L;

  $placeholder_ids = $info["placeholder_ids"];

  mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_placeholders WHERE published_form_id = $published_form_id");
  foreach ($placeholder_ids as $pid)
  {
  	if (!isset($info["placeholder_{$pid}"]))
  	  continue;

  	$value = "";
  	if (is_array($info["placeholder_{$pid}"]))
  	{
  		$value = implode("|", $info["placeholder_{$pid}"]);
  	}
  	else
  	{
  		$value = $info["placeholder_{$pid}"];
  	}

  	$value = ft_sanitize($value);

  	mysql_query("
  	  INSERT INTO {$g_table_prefix}module_form_builder_form_placeholders (published_form_id, placeholder_id, placeholder_value)
  	  VALUES ($published_form_id, $pid, '$value')
  	");
  }

  return array(true, $L["notify_form_placeholders_updated"]);
}


/**
 * Called in the Edit Form -> Publish tab. This converts an Internal or External form to Form Builder form.
 *
 * @param integer $form_id
 */
function fb_convert_form_to_form_builder_form($form_id)
{
  global $g_table_prefix, $L;

  $query = mysql_query("
    UPDATE {$g_table_prefix}forms
    SET    form_type = 'form_builder'
    WHERE  form_id = $form_id
  ");

  return array(true, $L["notify_form_converted_to_form_builder"]);
}


/**
 * Returns the last number + 1 for new template set creation.
 *
 * @return integer
 */
function fb_get_next_published_form_order($form_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT list_order
    FROM   {$g_table_prefix}module_form_builder_forms
    WHERE  form_id = $form_id
    ORDER BY list_order DESC
    LIMIT 1
  ");

  $result = mysql_fetch_assoc($query);

  $new_list_order = 1;
  if (!empty($result))
  {
    $new_list_order = $result["list_order"] + 1;
  }

  return $new_list_order;
}


/**
 * Called after the user deletes a published form. It updates the order of the remaining
 * published forms. Also called by the administrator when re-sorting the published forms listed on the Edit
 * Form -> Publish tab.
 *
 * @param integer $form_id
 * @param array $info
 */
function fb_update_published_form_order($form_id, $info = array())
{
  global $g_table_prefix, $L;

  if (empty($info))
  {
    $query = mysql_query("
      SELECT published_form_id
      FROM   {$g_table_prefix}module_form_builder_forms
      WHERE  form_id = $form_id
      ORDER BY list_order ASC
    ");

    $new_list_order = 1;
    while ($row = mysql_fetch_assoc($query))
    {
  	  $published_form_id = $row["published_form_id"];
      @mysql_query("
        UPDATE {$g_table_prefix}module_form_builder_forms
        SET    list_order = $new_list_order
        WHERE  published_form_id = $published_form_id
      ");
     $new_list_order++;
    }
  }
  else
  {
    $sortable_id = "form_builder_form_list";
	  $published_form_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

	  $order = 1;
	  foreach ($published_form_ids as $published_form_id)
	  {
	  	mysql_query("
	  	  UPDATE {$g_table_prefix}module_form_builder_forms
	  	  SET    list_order = $order
	  	  WHERE  published_form_id = $published_form_id
	  	");
	  	$order++;
	  }
  }

  return array(true, $L["notify_published_forms_updated"]);
}


/**
 * Called when the user clicks the "Save" button in the Builder popup.
 *
 * @param array $info
 */
function fb_save_builder_settings($info)
{
  global $g_table_prefix;

  // optional. If this is set, the user is updating an existing published form
  $published_form_id = !empty($info["published_form_id"]) ? $info["published_form_id"] : "";

  $info = ft_sanitize($info);

  // main form settings
  $form_id = $info["form_id"];
  $view_id = $info["view_id"];
  $is_online = (isset($info["is_online"])) ? "yes" : "no";
  $template_set_id = $info["template_set_id"];
  $include_review_page        = isset($info["include_review_page"]) ? "yes" : "no";
  $include_thanks_page_in_nav = isset($info["include_thanks_page_in_nav"]) ? "yes" : "no";
  $thankyou_page_content     = $info["thankyou_page_content"];
  $form_offline_page_content = $info["form_offline_page_content"];
  $offline_date              = "";
  if (!empty($info["offline_date"]) && preg_match("/\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}/", $info["offline_date"]))
  {
    list($date, $time) = explode(" ", $info["offline_date"]);
    list($month, $day, $year) = explode("/", $date);
    $offline_date = "{$year}-{$month}-{$day} $time";
  }

  $filename = $info["filename"];
  if (!preg_match("/\.php$/", $filename))
    $filename .= ".php";

  $folder_path = $info["folder_path"];
  $folder_url  = $info["folder_url"];
  $review_page_title = $info["review_page_title"];
  $thankyou_page_title = $info["thankyou_page_title"];

  if (empty($published_form_id))
  {
  	$list_order = fb_get_next_published_form_order($form_id);
    $query = mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_forms (is_online, is_published, form_id, view_id,
        set_id, filename, folder_path, folder_url, include_review_page, include_thanks_page_in_nav, thankyou_page_content,
        form_offline_page_content, review_page_title, thankyou_page_title, list_order)
      VALUES ('$is_online', 'no', $form_id, $view_id, $template_set_id, '$filename', '$folder_path', '$folder_url',
        '$include_review_page', '$include_thanks_page_in_nav', '$thankyou_page_content', '$form_offline_page_content',
        '$review_page_title', '$thankyou_page_title', $list_order)
    ");
    $published_form_id = mysql_insert_id();
  }
  else
  {
    $query = mysql_query("
      UPDATE {$g_table_prefix}module_form_builder_forms
      SET    is_online = '$is_online',
             form_id = $form_id,
             view_id = $view_id,
             set_id = $template_set_id,
             filename = '$filename',
             folder_path = '$folder_path',
             folder_url = '$folder_url',
             include_review_page = '$include_review_page',
             include_thanks_page_in_nav = '$include_thanks_page_in_nav',
             thankyou_page_content = '$thankyou_page_content',
             form_offline_page_content = '$form_offline_page_content',
             review_page_title = '$review_page_title',
             thankyou_page_title = '$thankyou_page_title',
             offline_date = '$offline_date'
      WHERE  published_form_id = $published_form_id
    ") or die(mysql_error());
  }

  if ($query)
  {
	  mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_templates WHERE published_form_id = $published_form_id");
	  $template_data = array(
	    "page_layout"       => $info["page_layout_template_id"],
	    "header"            => $info["header_template_id"],
	    "footer"            => $info["footer_template_id"],
	    "navigation"        => $info["navigation_template_id"],
	    "continue_block"    => $info["continue_block_template_id"],
	    "error_message"     => $info["error_message_template_id"],
	    "form_page"         => $info["form_page_template_id"],
	    "review_page"       => $info["review_page_template_id"],
	    "thankyou_page"     => $info["thankyou_page_template_id"],
	    "form_offline_page" => $info["form_offline_page_template_id"]
	  );

	  while (list($key, $template_id) = each($template_data))
	  {
	    mysql_query("
	      INSERT INTO {$g_table_prefix}module_form_builder_form_templates (published_form_id, template_type, template_id)
	      VALUES ($published_form_id, '$key', $template_id)
	    ") or die(mysql_error());
	  }
  }

  // now add the placeholders
  $placeholder_ids = (isset($info["placeholder_ids"])) ? $info["placeholder_ids"] : array();
  mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_form_placeholders WHERE published_form_id = $published_form_id");
  foreach ($placeholder_ids as $placeholder_id)
  {
  	// for checkbox groups and multi-selects that don't have any selections, there won't be any values here
  	if (!isset($info["placeholder_{$placeholder_id}"]))
  	  continue;

  	// again, for checkbox groups and multi-select fields
  	if (is_array($info["placeholder_{$placeholder_id}"]))
      $values = implode("|", $info["placeholder_{$placeholder_id}"]);
  	else
      $values = $info["placeholder_{$placeholder_id}"];

    mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_form_placeholders (published_form_id, placeholder_id, placeholder_value)
      VALUES ($published_form_id, $placeholder_id, '$values')
    ");
  }

  return array(
    "success"           => 1,
    "published_form_id" => $published_form_id,
    "message"           => ""
  );
}


function fb_get_generated_form_content($published_form_id, $filename)
{
	global $g_root_dir;

  $content = <<< END
<?php

/**
 * This page was created by the Form Tools Form Builder module.
 */
require_once('$g_root_dir/global/library.php');
\$published_form_id = $published_form_id;
\$filename  = "$filename";
require_once("\$g_root_dir/modules/form_builder/form.php");
END;

	return $content;
}
