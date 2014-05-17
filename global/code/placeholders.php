<?php


/**
 * Called on the Template Set -> Add Placeholder page.
 *
 * @param integer $set_id
 * @param array $info
 */
function fb_add_placeholder($set_id, $info)
{
  global $g_table_prefix;

  $info = ft_sanitize($info);

  $placeholder_label = $info["placeholder_label"];
  $placeholder       = $info["placeholder"];
  $field_type        = $info["field_type"];
  $field_orientation = $info["field_orientation"];
  $default_value     = $info["default_value"];
  $placeholder       = $info["placeholder"];

  // get the next highest
  $query = mysql_query("
    SELECT field_order
    FROM   {$g_table_prefix}module_form_builder_template_set_placeholders
    WHERE  set_id = $set_id
    ORDER BY field_order DESC
    LIMIT 1
  ");
  $result = mysql_fetch_assoc($query);

  $next_order = 1;
  if (!empty($result))
    $next_order = $result["field_order"] + 1;

  // add the main record first
  $query = mysql_query("
    INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholders (set_id, placeholder_label, placeholder,
      field_type, field_orientation, default_value, field_order)
    VALUES ($set_id, '$placeholder_label', '$placeholder', '$field_type', '$field_orientation', '$default_value', $next_order)
      ") or die(mysql_error());

  $placeholder_id = mysql_insert_id();

  // if this field had multiple options, add them too
  $placeholder_options = $info["placeholder_options"];
  if (in_array($field_type, array("select", "multi-select", "radios", "checkboxes")) && !empty($placeholder_options))
  {
  	$field_order = 1;
    foreach ($placeholder_options as $option)
    {
      if (empty($option))
        continue;

      mysql_query("
        INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholder_opts (placeholder_id, option_text, field_order)
        VALUES ($placeholder_id, '$option', $field_order)
          ");
      $field_order++;
    }
  }

  return array(true, "");
}


/**
 * Simple delete function.
 *
 * @param integer $placeholder_id
 */
function fb_delete_placeholder($placeholder_id)
{
  global $g_table_prefix, $L;

  $placeholder_info = fb_get_placeholder($placeholder_id);

  if (empty($placeholder_id) || !is_numeric($placeholder_id))
    return array(false, $L["notify_delete_invalid_placeholder_id"]);

  $result = mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholders WHERE placeholder_id = $placeholder_id");

  if (mysql_affected_rows() > 0)
  {
  	$result = mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholder_opts WHERE placeholder_id = $placeholder_id");

  	if (!empty($placeholder_info) && isset($placeholder_info["set_id"]))
      fb_update_placeholder_order($placeholder_info["set_id"]);

    return array(true, $L["notify_placeholder_deleted"]);
  }
  else
  {
    return array(true, $L["notify_placeholder_not_deleted"]);
  }
}


/**
 * Called on the Template Set -> Edit Placeholder page.
 *
 * @param integer $set_id
 * @param array $info
 */
function fb_update_placeholder($placeholder_id, $info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);

  $placeholder_label = $info["placeholder_label"];
  $placeholder       = $info["placeholder"];
  $field_type        = $info["field_type"];
  $field_orientation = $info["field_orientation"];
  $default_value     = $info["default_value"];
  $placeholder       = $info["placeholder"];

  // add the main record first
  $query = mysql_query("
    UPDATE {$g_table_prefix}module_form_builder_template_set_placeholders
    SET    placeholder_label = '$placeholder_label',
           placeholder = '$placeholder',
           field_type = '$field_type',
           field_orientation = '$field_orientation',
           default_value = '$default_value'
    WHERE  placeholder_id = $placeholder_id
      ") or die(mysql_error());


  // if this field had multiple options, add them too
  $placeholder_options = isset($info["placeholder_options"]) ? $info["placeholder_options"] : array();
  mysql_query("
    DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholder_opts
    WHERE placeholder_id = $placeholder_id
  ");

  if (in_array($field_type, array("select", "multi-select", "radios", "checkboxes")) && !empty($placeholder_options))
  {
    $field_order = 1;
    foreach ($placeholder_options as $option)
    {
      if (empty($option))
        continue;

      mysql_query("
        INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholder_opts (placeholder_id, option_text, field_order)
        VALUES ($placeholder_id, '$option', $field_order)
          ");
      $field_order++;
    }
  }

  return array(true, $L["notify_placeholder_updated"]);
}


function fb_get_placeholder($placeholder_id)
{
	global $g_table_prefix;

	$result = mysql_query("
	  SELECT *
	  FROM   {$g_table_prefix}module_form_builder_template_set_placeholders
	  WHERE  placeholder_id = $placeholder_id
	");

	$result = mysql_fetch_assoc($result);

	$options_query = mysql_query("
	  SELECT *
	  FROM   {$g_table_prefix}module_form_builder_template_set_placeholder_opts
	  WHERE  placeholder_id = $placeholder_id
	  ORDER BY field_order
	");
	$options = array();
	while ($row = mysql_fetch_assoc($options_query))
	  $options[] = $row;

  $result["options"] = $options;

	return $result;
}


/**
 * Returns all placeholders for a template set.
 *
 * @param integer $set_id
 */
function fb_get_placeholders($set_id)
{
  global $g_table_prefix, $L;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_template_set_placeholders
    WHERE  set_id = $set_id
    ORDER BY field_order
  ");

  $results = array();
  while ($row = mysql_fetch_assoc($query))
  {
  	$placeholder_id = $row["placeholder_id"];

  	$options_query = mysql_query("
  	  SELECT *
  	  FROM   {$g_table_prefix}module_form_builder_template_set_placeholder_opts
  	  WHERE  placeholder_id = $placeholder_id
  	  ORDER BY field_order
  	");

  	$options = array();
  	while ($row2 = mysql_fetch_assoc($options_query))
  	{
  	  $options[] = $row2;
  	}
  	$row["options"] = $options;
    $results[] = $row;
  }

  return $results;
}

/**
 * Called on the main Placeholders page - it deletes unwanted placeholders and re-orders those the
 * user wants to keep.
 *
 * @param array $info
 */
function fb_update_placeholders($info)
{
  global $g_table_prefix, $L;

  $sortable_id = $info["sortable_id"];

  // delete any unwanted placeholders
  $deleted_placeholder_ids_str = $info["{$sortable_id}_sortable__deleted_rows"];
  if (!empty($deleted_placeholder_ids_str))
  {
  	mysql_query("
  	  DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholders
  	  WHERE placeholder_id IN ($deleted_placeholder_ids_str)
  	");
  	mysql_query("
      DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholder_opts
      WHERE placeholder_id IN ($deleted_placeholder_ids_str)
    ");
  }

  $placeholder_ids = explode(",", $info["{$sortable_id}_sortable__rows"]);

  $order = 1;
  foreach ($placeholder_ids as $placeholder_id)
  {
    mysql_query("
      UPDATE {$g_table_prefix}module_form_builder_template_set_placeholders
      SET    field_order = $order
      WHERE  placeholder_id = $placeholder_id
    ");
    $order++;
  }

  return array(true, $L["notify_placeholders_updated"]);
}


function fb_get_num_placeholders($set_id)
{
	global $g_table_prefix;

	$query = mysql_query("
	  SELECT count(*) as c
	  FROM   {$g_table_prefix}module_form_builder_template_set_placeholders
	  WHERE  set_id = $set_id
	");
	$result = mysql_fetch_assoc($query);

	return $result["c"];
}


/**
 * Called after a placeholder gets deleted.
 *
 * @param integer $set_id
 */
function fb_update_placeholder_order($set_id)
{
  global $g_table_prefix;

  $placeholders = fb_get_placeholders($set_id);

  $list_order = 1;
  foreach ($placeholders as $info)
  {
  	$placeholder_id = $info["placeholder_id"];
  	@mysql_query("
  	  UPDATE {$g_table_prefix}module_form_builder_template_set_placeholders
  	  SET    field_order = $list_order
  	  WHERE  placeholder_id = $placeholder_id
  	");
    $list_order++;
  }
}


/**
 * Called by the Form Builder to generate the markup for the Placeholders section in the sidebar.
 *
 * @param integer $set_id
 * @param array $placeholders
 * @param array $placeholder_hash
 */
function fb_generate_template_set_placeholders_html($set_id, $placeholders, $placeholder_hash = array())
{
  global $g_table_prefix, $g_root_dir, $L;

  $smarty = fb_create_new_smarty_instance("single");
  $smarty->assign("placeholders", $placeholders);
  $smarty->assign("placeholder_hash", $placeholder_hash);
  $smarty->assign("L", $L);

  $html = $smarty->fetch("../../modules/form_builder/smarty/placeholders_html.tpl");

  return $html;
}