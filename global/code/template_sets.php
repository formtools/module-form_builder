<?php

// TODO
$GLOBALS["g_template_types"] = array(
  "Page Types" => array(
    "form_page"         => "phrase_form_page",
    "review_page"       => "phrase_review_page",
    "thankyou_page"     => "phrase_thankyou_page",
    "form_offline_page" => "phrase_form_offline_page"
  ),
  "Page Elements" => array(
    "header"         => "word_header",
    "footer"         => "word_footer",
    "navigation"     => "word_navigation",
    "continue_block" => "phrase_continue_block",
    "error_message"  => "phrase_error_message"
  ),
  "Other" => array(
    "page_layout"   => "phrase_page_layout",
    "code_block"    => "phrase_code_block"
  )
);


function fb_get_template_sets($only_return_complete = true)
{
  global $g_table_prefix;

  $is_complete_clause = ($only_return_complete) ? "AND is_complete = 'yes'" : "";
  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_template_sets
    WHERE  1=1
           $is_complete_clause
    ORDER BY list_order ASC
  ");

  $results = array();
  while ($row = mysql_fetch_assoc($query))
  {
    $set_id = $row["set_id"];

    $row["templates"]    = fb_get_templates($set_id);
    $row["resources"]    = fb_get_resources($set_id);
    $row["placeholders"] = fb_get_placeholders($set_id);
    $results[] = $row;
  }

  return $results;
}


/**
 * This returns the set ID of the first complete template set, as determined by the ordering on the
 * Template Sets page in the module.
 *
 * return integer the set ID, or the empty string if there's no complete template set
 */
function fb_get_first_template_set_id()
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT set_id
    FROM   {$g_table_prefix}module_form_builder_template_sets
    WHERE  is_complete = 'yes'
    ORDER BY list_order ASC
    LIMIT 1
  ");

  $result = mysql_fetch_assoc($query);
  $set_id = "";
  if (!empty($result))
    $set_id = $result["set_id"];

  return $set_id;
}


function fb_get_template_set($set_id, $options = array())
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_template_sets
    WHERE  set_id = $set_id
  ");

  $template_set_info = mysql_fetch_assoc($query);
  $template_set_info["templates"]    = fb_get_templates($set_id, $options);
  $template_set_info["resources"]    = fb_get_resources($set_id);
  $template_set_info["placeholders"] = fb_get_placeholders($set_id);

  return $template_set_info;
}


/**
 * Deletes an entire template set and all associated resources, placeholders and templates. This
 * relies on the interface preventing the user from deleting
 *
 * @param integer $set_id
 */
function fb_delete_template_set($set_id)
{
  global $g_table_prefix, $L;

  if (empty($set_id) || !is_numeric($set_id))
  {
    return array(false, $L["notify_template_set_not_deleted"]);
  }

  // remove all the placeholders. This is done through a separate function, since the placeholder options need
  // deleting as well, which takes more work to identify the records
  $placeholders = fb_get_placeholders($set_id);
  foreach ($placeholders as $placeholder_info)
  {
    fb_delete_placeholder($placeholder_info["placeholder_id"]);
  }

  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_sets WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_templates WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_resources WHERE template_set_id = $set_id");

  return array(true, $L["notify_template_set_deleted"]);
}


/**
 * Creates a new template set.
 *
 * @param string $template_set_name
 * @param integer
 */
function fb_create_new_template_set($template_set_name, $original_set_id)
{
  global $g_table_prefix;

  $list_order = fb_get_new_template_set_order();
  $new_template_set_name = ft_sanitize($template_set_name);

  $response = array(
    "success" => 0,
    "message" => ""
  );

  if (empty($original_set_id))
  {
    $query = mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_template_sets (set_name, version, is_complete, list_order)
      VALUES ('$new_template_set_name', '1.0', 'no', $list_order)
    ");

    if ($query)
    {
      $response = array(
        "success" => 1,
        "message" => mysql_insert_id()
      );
    }
  }

  // here, make a copy of the entire template set
  else
  {
    $template_set = fb_get_template_set($original_set_id);

    // first, create the new template set record
    $template_set = ft_sanitize($template_set);
    $is_complete = $template_set["is_complete"];
    $version     = $template_set["version"];
    $description = $template_set["description"];

    $query = mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_template_sets (set_name, version, description, is_complete, list_order)
      VALUES ('$new_template_set_name', '$version', '$description', '$is_complete', $list_order)
    ");

    if ($query)
    {
      $set_id = mysql_insert_id();

      // now copy over the templates
      foreach ($template_set["templates"] as $template_info)
      {
        $template_type = $template_info["template_type"];
        $template_name = $template_info["template_name"];
        $content = $template_info["content"];
        $list_order = $template_info["list_order"];

        $query = mysql_query("
          INSERT INTO {$g_table_prefix}module_form_builder_templates (set_id, template_type, template_name,
            content, list_order)
          VALUES ($set_id, '$template_type', '$template_name', '$content', $list_order)
        ");

        if (!$query)
        {
          $error = mysql_error();
          fb_rollback_new_template_set($set_id);
          $response["message"] = "Sorry, there was a problem creating your template set when copying over the templates. Please report this problem in the forums: $error";
          return $response;
        }
      }

      // copy over the resources
      $resources = fb_get_resources($original_set_id);
      foreach ($resources as $resource_info)
      {
        $resource_type = $resource_info["resource_type"];
        $resource_name = ft_sanitize($resource_info["resource_name"]);
        $placeholder   = $resource_info["placeholder"];
        $content       = ft_sanitize($resource_info["content"]);

        $query = mysql_query("
          INSERT INTO {$g_table_prefix}module_form_builder_template_set_resources (resource_type, template_set_id,
            resource_name, placeholder, content)
          VALUES ('$resource_type', $set_id, '$resource_name', '$placeholder', '$content')
        ");

        if (!$query)
        {
          $error = mysql_error();
          fb_rollback_new_template_set($set_id);
          $response["message"] = "Sorry, there was a problem creating the template set when copying over the resources. Please report this problem in the forums: $error";
          return $response;
        }
      }

      // now copy over the placeholders
      $placeholders = fb_get_placeholders($original_set_id);
      $placeholders = ft_sanitize($placeholders);
      foreach ($placeholders as $placeholder_info)
      {
        $placeholder_label = $placeholder_info["placeholder_label"];
        $placeholder = $placeholder_info["placeholder"];
        $field_type = $placeholder_info["field_type"];
        $field_orientation = $placeholder_info["field_orientation"];
        $default_value = $placeholder_info["default_value"];
        $field_order = $placeholder_info["field_order"];

        $query = mysql_query("
          INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholders (set_id, placeholder_label, placeholder,
            field_type, field_orientation, default_value, field_order)
          VALUES ($set_id, '$placeholder_label', '$placeholder', '$field_type', '$field_orientation', '$default_value',
            $field_order)
        ") or die(mysql_error());

        if ($query)
        {
          $new_placeholder_id = mysql_insert_id();

          foreach ($placeholder_info["options"] as $option_info)
          {
            $option_text = $option_info["option_text"];
            $field_order = $option_info["field_order"];

            $query = mysql_query("
              INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholder_opts (placeholder_id,
                option_text, field_order)
              VALUES ($new_placeholder_id, '$option_text', $field_order)
            ") or die(mysql_error());
          }
        }
      }
    }

    $response = array(
      "success" => 1,
      "message" => $set_id
    );
  }

  return $response;
}


/**
 * Used during the creation of a new template set, when it's based on an existing one. If anything fails
 * in the process, it rolls back all insertions that may have occurred.
 *
 * @param integer $set_id
 */
function fb_rollback_new_template_set($set_id)
{
  global $g_table_prefix;

  if (empty($set_id))
    return;

  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_templates WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_sets WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholders WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_placeholder_opts WHERE set_id = $set_id");
  @mysql_query("DELETE FROM {$g_table_prefix}module_form_builder_template_set_resources WHERE set_id = $set_id");
}


/**
 * Called on the Info tab, this updates the name and description. It also checks to see whether all required
 * templates have been entered + assigns the "is_complete" value appropriately.
 *
 * @param unknown_type $set_id
 * @param unknown_type $info
 */
function fb_update_template_set_info($set_id, $info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);

  $missing_templates = fb_get_missing_template_set_templates($set_id);
  $is_complete = empty($missing_templates) ? "yes" : "no";

  $result = mysql_query("
    UPDATE {$g_table_prefix}module_form_builder_template_sets
    SET    set_name = '{$info["set_name"]}',
           description = '{$info["description"]}',
           version = '{$info["version"]}',
           is_complete = '$is_complete'
    WHERE  set_id = $set_id
  ");

  if ($result)
  {
    return array(true, $L["notify_template_set_updated"]);
  }
  else
  {
    return array(true, $L["notify_template_set_not_updated"] . mysql_error());
  }
}


/**
 * Simple helper function to determine whether or not a template set is complete. This checks to confirm
 * that the template set has defined each of the templates.
 *
 * @param integer $set_id
 */
function fb_get_missing_template_set_templates($set_id)
{
  $required_templates = array("form_page", "review_page", "thankyou_page", "form_offline_page", "header",
    "footer", "navigation", "continue_block", "page_layout", "error_message");

  $template_set = fb_get_template_set($set_id);

  $defined_templates = array();
  foreach ($template_set["templates"] as $template_info)
  {
    $defined_templates[] = $template_info["template_type"];
  }
  $missing_templates = array_diff($required_templates, $defined_templates);

  return $missing_templates;
}


function fb_get_template_type_name($template_type)
{
  global $g_template_types, $L;

  $name = "";
  while (list($group_name, $types) = each($g_template_types))
  {
    while (list($key, $lang_key) = each($types))
    {
      if ($key == $template_type)
      {
        $name = ft_eval_smarty_string("{\$" . $lang_key . "}", $L);
      }
    }
  }
  reset($g_template_types);

  return $name;
}


/**
 * Returns a list of forms that use a template set, and all their URLs.
 *
 * @param integer $set_id
 * @return array
 */
function fb_get_template_set_usage($set_id)
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT *
    FROM   {$g_table_prefix}module_form_builder_forms fb, {$g_table_prefix}forms f, {$g_table_prefix}views v
    WHERE  fb.set_id = $set_id AND
           fb.form_id = f.form_id AND
           fb.view_id = v.view_id
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
      "view_id"     => $row["view_id"],
      "view_name"   => $row["view_name"]
    );
  }

  return $results;
}


/**
 * Called on the main templates page in the module, when the user manually re-sorted the template sets.
 *
 * @param unknown_type $info
 */
function fb_update_template_set_order($info)
{
  global $g_table_prefix, $L;

  $info = ft_sanitize($info);
  $sortable_id   = $info["sortable_id"];
  $sortable_rows = explode(",", $info["{$sortable_id}_sortable__rows"]);

  $order = 1;
  foreach ($sortable_rows as $set_id)
  {
    mysql_query("
      UPDATE {$g_table_prefix}module_form_builder_template_sets
      SET    list_order = $order
      WHERE  set_id = $set_id
    ");
    $order++;
  }

  return array(true, $L["notify_template_set_order_updated"]);
}


/**
 * Returns the last number + 1 for new template set creation.
 *
 * @return integer
 */
function fb_get_new_template_set_order()
{
  global $g_table_prefix;

  $query = mysql_query("
    SELECT list_order
    FROM   {$g_table_prefix}module_form_builder_template_sets
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
 * Used in the Form Builder to provide a default template set + templates when the page first loads.
 *
 * @return mixed
 */
function fb_get_default_template_set()
{
  // first, check the there's at least one complete template set available. If not, we're not
  // going to get very far
  $set_id = fb_get_first_template_set_id();
  if (empty($set_id))
    return "";

  $templates = fb_get_templates($set_id);

  return array(
    "set_id"    => $set_id,
    "templates" => $templates
  );
}


/**
 * Returns an ordered list of template IDs in a template set.
 *
 * @param integer $set_id
 */
function fb_get_template_ids($set_id)
{
  global $g_table_prefix;

  $templates = fb_get_templates($set_id);
  $template_ids = array();
  foreach ($templates as $template_info)
  {
    $template_ids[] = $template_info["template_id"];
  }

  return $template_ids;
}


function fb_get_template_set_prev_next_links($set_id)
{
  $template_sets = fb_get_template_sets(false);

  $sorted_set_ids = array();
  foreach ($template_sets as $set_info)
  {
    $sorted_set_ids[] = $set_info["set_id"];
  }
  $current_index = array_search($set_id, $sorted_set_ids);

  $return_info = array("prev_set_id" => "", "next_set_id" => "");
  if ($current_index === 0)
  {
    if (count($sorted_set_ids) > 1)
      $return_info["next_set_id"] = $sorted_set_ids[$current_index+1];
  }
  else if ($current_index === count($sorted_set_ids)-1)
  {
    if (count($sorted_set_ids) > 1)
      $return_info["prev_set_id"] = $sorted_set_ids[$current_index-1];
  }
  else
  {
    $return_info["prev_set_id"] = $sorted_set_ids[$current_index-1];
    $return_info["next_set_id"] = $sorted_set_ids[$current_index+1];
  }

  return $return_info;
}


/**
 * This generates both the main default_sets.php file (found in this folder) used for fresh installations,
 * and for generating a custom template set file for import/export.
 *
 * @param mixed $set_id the specific set ID that you
 */
function fb_create_default_template_set_file($set_id = "")
{
  global $g_table_prefix;

  $php_lines = array();
  $php_lines[] = "\$g_default_sets = array();\n";

  $template_sets = array();
  if (empty($set_id))
  {
    $template_sets = fb_get_template_sets(false);
  }
  else
  {
    $template_sets[] = fb_get_template_set($set_id);
  }

  $set_order = 1;
  foreach ($template_sets as $template_set_info)
  {
    $php_lines[] = "\$g_default_sets[] = array(";

    $set_name    = $template_set_info["set_name"];
    $version     = $template_set_info["version"];
    $description = ft_sanitize($template_set_info["description"]);

    // escape values
    $php_lines[] = "  \"set_name\"    => \"{$set_name}\",";
    $php_lines[] = "  \"version\"     => \"{$version}\",";
    $php_lines[] = "  \"description\" => \"{$description}\",";
    $php_lines[] = "  \"is_complete\" => \"{$template_set_info["is_complete"]}\",";
    $php_lines[] = "  \"list_order\"  => $set_order,\n";
    $php_lines[] = "  // templates";

    // templates
    if (empty($template_set_info["templates"]))
    {
      $php_lines[] = "  \"templates\" => array(),\n";
    }
    else
    {
      $php_lines[] = "  \"templates\" => array(";
      $template_lines = array();
      foreach ($template_set_info["templates"] as $template_info)
      {
        $content = preg_replace("/\r?\n/", "\\n", $template_info["content"]);
        $content = preg_replace("/\"/", "\\\"", $content);
        $content = preg_replace('/\$/', '\\\$', $content);
        $template_lines[] =<<< END
    array(
      "template_type" => "{$template_info["template_type"]}",
      "template_name" => "{$template_info["template_name"]}",
      "content"       => "{$content}"
    )
END;
      }
      $php_lines[] = implode(",\n", $template_lines);
      $php_lines[] = "  ),\n";
    }

    $php_lines[] = "  // resources";

    // resources
    if (empty($template_set_info["resources"]))
    {
      $php_lines[] = "  \"resources\" => array(),\n";
    }
    else
    {
      $php_lines[] = "  \"resources\" => array(";
      $resource_lines = array();
      foreach ($template_set_info["resources"] as $resource_info)
      {
        $content = preg_replace("/\r\n/", "\\n", $resource_info["content"]);
        $content = preg_replace("/\"/", "\\\"", $content);
        $content = preg_replace('/\$/', '\\\$', $content);
        $resource_lines[] =<<< END
    array(
      "resource_type" => "{$resource_info["resource_type"]}",
      "resource_name" => "{$resource_info["resource_name"]}",
      "placeholder"   => "{$resource_info["placeholder"]}",
      "content"       => "{$content}",
      "last_updated"  => "{$resource_info["last_updated"]}"
    )
END;
      }
      $php_lines[] = implode(",\n", $resource_lines);
      $php_lines[] = "  ),\n";
    }

    $php_lines[] = "  // placeholders";

    // placeholders
    if (empty($template_set_info["placeholders"]))
    {
      $php_lines[] = "  \"placeholders\" => array()";
    }
    else
    {
      $php_lines[] = "  \"placeholders\" => array(";
      $resource_lines = array();
      foreach ($template_set_info["placeholders"] as $placeholder_info)
      {
        $str =<<< END
    array(
      "placeholder_label" => "{$placeholder_info["placeholder_label"]}",
      "placeholder"       => "{$placeholder_info["placeholder"]}",
      "field_type"        => "{$placeholder_info["field_type"]}",
      "field_orientation" => "{$placeholder_info["field_orientation"]}",
      "default_value"     => "{$placeholder_info["default_value"]}",

END;

        // placeholder options
        if (empty($placeholder_info["options"]))
        {
          $str .= "      \"options\" => array()\n";
        }
        else
        {
          $str .= "      \"options\" => array(\n";

          $option_lines = array();
          foreach ($placeholder_info["options"] as $option_info)
          {
            $option_text = preg_replace("/\"/", "\\\"", $option_info["option_text"]);
            $option_lines[] = "        array(\"option_text\" => \"$option_text\")";
          }

          $str .= implode(",\n", $option_lines) . "\n      )\n";
        }

        $str .= "    )";

        $resource_lines[] = $str;
      }
      $php_lines[] = implode(",\n", $resource_lines);
      $php_lines[] = "  )";
    }

    $php_lines[] = ");\n";

    $set_order++;
  }

  return implode("\n", $php_lines);
}


function fb_import_template_set($filename)
{
	global $g_root_dir, $L;

  if (!is_file("$g_root_dir/modules/form_builder/share/$filename"))
  {
    return array(false, $L["notify_invalid_template_set_filename"]);
  }

  require_once("$g_root_dir/modules/form_builder/share/$filename");
  fb_import_template_set_data($g_default_sets);

  return array(true, $L["notify_template_set_imported"]);
}


/**
 * Helper function to determine whether or not a Template Set actually exists or not.
 *
 * @param integer $set_id
 */
function fb_check_template_set_exists($set_id)
{
  global $g_table_prefix;

  if (empty($set_id) || !is_numeric($set_id))
    return false;

  $query = mysql_query("
    SELECT count(*) as c
    FROM   {$g_table_prefix}module_form_builder_template_sets
    WHERE  set_id = $set_id
  ");

  $result = mysql_fetch_assoc($query);

  return $result["c"] == 1;
}



/**
 * Generates an export file for a Template Set.
 *
 * @param integer $set_id
 */
function fb_generate_template_set_export_file($set_id)
{
  global $g_table_prefix;

  if (!fb_check_template_set_exists($set_id))
  {
    echo "Invalid Template Set ID";
    exit;
  }

  $php = fb_create_default_template_set_file($set_id);

  echo "<textarea style=\"width: 100%; height: 100%\"><?php\n\n$php</textarea>";
}


function fb_import_template_set_data($template_sets)
{
	global $g_table_prefix;

  $template_set_order = fb_get_new_template_set_order();
  foreach ($template_sets as $set_info)
  {
  	$set_info = ft_sanitize($set_info);

  	// insert the new template set
    mysql_query("
      INSERT INTO {$g_table_prefix}module_form_builder_template_sets (set_name, version, description,
        is_complete, list_order)
      VALUES ('{$set_info["set_name"]}', '{$set_info["version"]}', '{$set_info["description"]}',
        '{$set_info['is_complete']}', '$template_set_order')
    ");
    $set_id = mysql_insert_id();

    // templates
    $template_order = 1;
    foreach ($set_info["templates"] as $template_info)
    {
    	mysql_query("
    	  INSERT INTO {$g_table_prefix}module_form_builder_templates (set_id, template_type, template_name,
    	    content, list_order)
    	  VALUES ($set_id, '{$template_info["template_type"]}', '{$template_info["template_name"]}',
    	    '{$template_info["content"]}', $template_order)
    	");
      $template_order++;
    }

    // resources
    $resource_order = 1;
    foreach ($set_info["resources"] as $resource_info)
    {
    	mysql_query("
    	  INSERT INTO {$g_table_prefix}module_form_builder_template_set_resources (resource_type, template_set_id,
    	    resource_name, placeholder, content, last_updated, list_order)
    	  VALUES ('{$resource_info["resource_type"]}', $set_id, '{$resource_info["resource_name"]}',
    	    '{$resource_info["placeholder"]}', '{$resource_info["content"]}', '{$resource_info["last_updated"]}',
    	    $resource_order)
    	");
      $resource_order++;
    }

    // placeholders
    $placeholder_order = 1;
    foreach ($set_info["placeholders"] as $placeholder_info)
    {
    	mysql_query("
    	  INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholders (set_id, placeholder_label,
    	    placeholder, field_type, field_orientation, default_value, field_order)
    	  VALUES ($set_id, '{$placeholder_info["placeholder_label"]}', '{$placeholder_info["placeholder"]}',
    	    '{$placeholder_info["field_type"]}', '{$placeholder_info["field_orientation"]}',
    	    '{$placeholder_info["default_value"]}', $placeholder_order)
    	");
    	$placeholder_id = mysql_insert_id();

    	// placeholder options
    	$option_order = 1;
    	foreach ($placeholder_info["options"] as $option_info)
    	{
    		mysql_query("
    		  INSERT INTO {$g_table_prefix}module_form_builder_template_set_placeholder_opts (placeholder_id,
    		    option_text, field_order)
    		  VALUES ($placeholder_id, '{$option_info["option_text"]}', $option_order)
    		");
    		$option_order++;
    	}

      $placeholder_order++;
    }

    $template_set_order++;
  }
}
