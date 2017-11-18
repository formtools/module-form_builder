<?php


/**
 * Creates a new template.
 *
 * @param integer $set_id
 * @param array $info
 */
function fb_create_new_template($set_id, $info)
{
  global $g_table_prefix;

  $template_name = ft_sanitize($info["template_name"]);
  $template_type = ft_sanitize($info["template_type"]);

  $list_order = fb_get_next_template_order($set_id);

  // if the template set had one or more templates already in it, the Create New Template dialog
  // would have offered the option to create the template based on an existing one.
  $is_based_on_existing_template = false;
	if ($info["new_template_source"] == "existing_template")
 	{
 		$is_based_on_existing_template = true;
 		$old_template_info = fb_get_template($info["source_template_id"]);
    $old_template_info = ft_sanitize($old_template_info);

    // use the same type as the original!
    $template_type = $old_template_info["template_type"];
    $content       = $old_template_info["content"];

    $result = mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_templates (set_id, template_name, template_type,
        content, list_order)
      VALUES ($set_id, '$template_name', '$template_type', '$content', $list_order)
    ");
 	}
  else
  {
    $result = mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_templates (set_id, template_name, template_type, list_order)
      VALUES ($set_id, '$template_name', '$template_type', $list_order)
    ");
  }

  $success = 0;
  $message = "";

  // if the template was added, check the template set to see if it's now complete or not
  if ($result)
  {
    $success = 1;
    $message = mysql_insert_id();

    $missing_templates = fb_get_missing_template_set_templates($set_id);
    if (empty($missing_templates))
    {
      mysql_query("
        UPDATE {$g_table_prefix}module_form_builder_template_sets
        SET    is_complete = 'yes'
        WHERE  set_id = $set_id
      ");
    }
  }

  return array(
    "success" => $success,
    "message" => $message
  );
}


function fb_get_template($template_id)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT *
	  FROM   {$g_table_prefix}module_form_builder_templates
	  WHERE  template_id = $template_id
	");

	$result = mysql_fetch_assoc($query);
	return $result;
}


/**
 * Returns all templates in a template set.
 *
 * @param $set_id
 * @return array
 */
function fb_get_templates($set_id, $options = array())
{
	global $g_table_prefix;

	$get_template_usage = (isset($options["get_template_usage"]) && $options["get_template_usage"]) ? true : false;

  $templates_query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_templates
    WHERE  set_id = $set_id
    ORDER BY list_order
  ");

  $templates = array();
  while ($row = mysql_fetch_assoc($templates_query))
  {
  	if ($get_template_usage)
  	{
      $template_id = $row["template_id"];
      $row["usage"] = fb_get_template_usage($template_id);
  	}
    $templates[] = $row;
  }

  return $templates;
}


/**
 * Returns all templates in a template set.
 *
 * @param $set_id
 * @return array
 */
function fb_get_templates_grouped_by_type($set_id)
{
	global $g_table_prefix;

  $templates_query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_templates
    WHERE  set_id = $set_id
    ORDER BY list_order
  ");

  $grouped_templates = array();
  while ($row = mysql_fetch_assoc($templates_query))
  {
  	if (!array_key_exists($row["template_type"], $grouped_templates))
  		$grouped_templates[$row["template_type"]] = array();

    $grouped_templates[$row["template_type"]][] = $row;
  }

  return $grouped_templates;
}



/**
 * Updates the template content.
 *
 * @param unknown_type $template_info
 */
function fb_update_template($template_info)
{
	global $g_table_prefix, $L;

  $template_info = ft_sanitize($template_info);

  $result = mysql_query("
    UPDATE {$g_table_prefix}module_form_builder_templates
    SET    template_name = '{$template_info["template_name"]}',
           content = '{$template_info["template_content"]}'
    WHERE  template_id = {$template_info["template_id"]}
    LIMIT 1
  ");

  if ($result)
  {
    return array(true, $L["notify_template_updated"]);
  }
  else
  {
    return array(false, $L["notify_template_not_updated"] . mysql_error());
  }
}


function fb_delete_template($template_id)
{
  global $g_table_prefix, $L;

  $template_info = fb_get_template($template_id);
  $delete_query = mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_templates WHERE template_id = $template_id");

  if (mysql_affected_rows() == 1)
  {
  	$set_id = $template_info["set_id"];
    $missing_templates = fb_get_missing_template_set_templates($set_id);
    if (!empty($missing_templates))
    {
      mysql_query("
        UPDATE {$g_table_prefix}module_form_builder_template_sets
        SET    is_complete = 'no'
        WHERE  set_id = $set_id
      ");
    }

  	return array(true, $L["notify_template_deleted"]);
  }
  else
  {
    return array(false, $L["notify_template_not_deleted"]);
  }
}


function fb_get_template_type($set_id, $type)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT *
	  FROM   {$g_table_prefix}module_form_builder_templates
	  WHERE  set_id = $set_id AND
	         template_type = '$type'
    ORDER BY list_order
	");

	$templates = array();
	while ($row = mysql_fetch_assoc($query))
	{
	  $templates[] = $row;
	}

	return $templates;
}


function fb_update_template_order($set_id, $info)
{
	global $g_table_prefix, $L;

  $sortable_id = $info["sortable_id"];
  $template_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

  $order = 1;
  foreach ($template_ids as $template_id)
  {
  	mysql_query("
  	  UPDATE {$g_table_prefix}module_form_builder_templates
  	  SET    list_order = $order
  	  WHERE  template_id = $template_id
  	");
  	$order++;
  }

	return array(true, $L["notify_template_order_updated"]);
}


function fb_get_next_template_order($set_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT list_order
    FROM   {$g_table_prefix}module_form_builder_templates
    WHERE  set_id = $set_id
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


function fb_generate_template_set_templates_html($set_id, $selected_templates = array())
{
  global $g_table_prefix, $g_root_dir, $L;

  $grouped_templates = fb_get_templates_grouped_by_type($set_id);

  $smarty = fb_create_new_smarty_instance("single");
  $smarty->assign("grouped_templates", $grouped_templates);
  $smarty->assign("selected_templates", $selected_templates);
  $smarty->assign("L", $L);

  $html = $smarty->fetch("../../modules/form_builder/smarty/templates_html.tpl");

  return $html;
}


/**
 * Returns a list of forms that use a template set, and all their URLs.
 *
 * @param integer $set_id
 * @return array
 */
function fb_get_template_usage($template_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT f.form_id, f.form_name, fb.filename, fb.folder_path, fb.folder_url
    FROM   {$g_table_prefix}module_form_builder_form_templates t,
           {$g_table_prefix}module_form_builder_forms fb,
           {$g_table_prefix}forms f
    WHERE  t.template_id = $template_id AND
           t.published_form_id = fb.published_form_id AND
           fb.form_id = f.form_id
  ");

  $results = array();
  while ($row = mysql_fetch_assoc($query))
  {
  	$form_id = $row["form_id"];
  	if (!array_key_exists($form_id, $results))
  	{
  	  $results[$form_id] = array(
  	    "form_name" => $row["form_name"],
  	    "usage"     => array()
  	  );
  	}

    $results[$form_id]["usage"][] = array(
      "filename"    => $row["filename"],
      "folder_url"  => $row["folder_url"],
      "folder_path" => $row["folder_path"],
      "full_url"    => $row["folder_url"] . "/" . $row["filename"],
    );
  }

  return $results;
}

