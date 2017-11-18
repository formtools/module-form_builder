<?php


function fb_add_resource($set_id, $resource_name, $placeholder, $resource_type)
{
  global $g_table_prefix;

  $resource_name = ft_sanitize($resource_name);
  $placeholder   = ft_sanitize($placeholder);
  $resource_type = ft_sanitize($resource_type);

  // get the next list_order for this template set
  $list_order = fb_get_next_resource_list_order($set_id);
  $now = ft_get_current_datetime();

  $query = mysql_query("
    INSERT INTO {$g_table_prefix}module_form_builder_template_set_resources (resource_type, template_set_id,
      resource_name, placeholder, content, last_updated, list_order)
    VALUES ('$resource_type', $set_id, '$resource_name', '$placeholder', '', '$now', $list_order)
  ");

  if ($query)
  {
    return array(
      "success" => 1,
      "message" => mysql_insert_id()
    );
  }
  else
  {
  	return array(
  	  "success" => 0,
  	  "message" => ""
  	);
  }
}


function fb_get_resources($set_id)
{
	global $g_table_prefix;

	// ordering is temporary
	$query = mysql_query("
	  SELECT *
	  FROM   {$g_table_prefix}module_form_builder_template_set_resources
	  WHERE  template_set_id = $set_id
	  ORDER BY list_order
	");

	$results = array();
	while ($row = mysql_fetch_assoc($query))
	{
		$results[] = $row;
	}

	return $results;
}


function fb_get_resource($resource_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_template_set_resources
    WHERE  resource_id = $resource_id
  ");

  $resource_info = mysql_fetch_assoc($query);

  return $resource_info;
}


function fb_update_resource($resource_id, $info)
{
	global $g_table_prefix, $L;

	$info = ft_sanitize($info);
  $now = ft_get_current_datetime();

	$query = mysql_query("
	  UPDATE {$g_table_prefix}module_form_builder_template_set_resources
	  SET    resource_name = '{$info["resource_name"]}',
	         resource_type = '{$info["resource_type"]}',
	         placeholder = '{$info["placeholder"]}',
	         content = '{$info["resource_content"]}',
	         last_updated = '$now'
	  WHERE  resource_id = $resource_id
	");

	if ($query)
	{
	  return array(true, $L["notify_resource_updated"]);
	}
	else
	{
		return array(true, $L["notify_resource_not_updated"] . mysql_error());
	}
}


function fb_delete_resource($resource_id)
{
	global $g_table_prefix, $L;

	if (empty($resource_id) || !is_numeric($resource_id))
	  return array(false, $L["notify_delete_invalid_resource_id"]);

	$result = mysql_query("
	  DELETE FROM {$g_table_prefix}module_form_builder_template_set_resources
	  WHERE resource_id = $resource_id
	");

	if (mysql_affected_rows() > 0)
	{
		return array(true, $L["notify_resource_deleted"]);
	}
	else
	{
		return array(true, $L["notify_resource_not_deleted"]);
	}
}


/**
 * Figures out the next available list order for a new template set resource.
 *
 * @param integer $set_id
 */
function fb_get_next_resource_list_order($set_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT list_order
    FROM   {$g_table_prefix}module_form_builder_template_set_resources
    WHERE  template_set_id = $set_id
    ORDER BY list_order DESC
    LIMIT 1
      ");

  $result = mysql_fetch_assoc($query);

  $next_list_order = 1;
  if (isset($result["list_order"]))
  {
  	$next_list_order = $result["list_order"]+1;
  }

  return $next_list_order;
}


function fb_update_resource_order($set_id, $info)
{
	global $g_table_prefix, $L;

  $sortable_id = $info["sortable_id"];

  $ordered_resource_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

  $order = 1;
  foreach ($ordered_resource_ids as $resource_id)
  {
    mysql_query("
      UPDATE {$g_table_prefix}module_form_builder_template_set_resources
      SET    list_order = $order
      WHERE  resource_id = $resource_id
    ");
    $order++;
  }

  return array(true, $L["notify_resource_order_updated"]);
}




