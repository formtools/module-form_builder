<?php


function form_builder__install($module_id)
{
	global $g_table_prefix, $g_root_dir, $g_root_url, $LANG;

	$queries = array();
	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_forms (
			published_form_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			is_online enum('yes','no') NOT NULL,
			is_published enum('yes','no') NOT NULL,
			form_id mediumint(9) NOT NULL,
			view_id mediumint(9) NOT NULL,
			set_id mediumint(9) NOT NULL,
			publish_date datetime DEFAULT NULL,
			filename varchar(255) NOT NULL,
			folder_path mediumtext NOT NULL,
			folder_url mediumtext NOT NULL,
			include_review_page enum('yes','no') NOT NULL,
			include_thanks_page_in_nav enum('yes','no') NOT NULL,
			thankyou_page_content mediumtext,
			form_offline_page_content mediumtext,
			review_page_title varchar(255) DEFAULT NULL,
			thankyou_page_title varchar(255) DEFAULT NULL,
			offline_date datetime NOT NULL,
			list_order smallint(6) NOT NULL,
			PRIMARY KEY (published_form_id)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_form_placeholders (
			published_form_id mediumint(9) NOT NULL,
			placeholder_id mediumint(9) NOT NULL,
			placeholder_value mediumtext NOT NULL,
			UNIQUE KEY published_form_id (published_form_id, placeholder_id)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_form_templates (
			published_form_id mediumint(9) NOT NULL,
			template_type varchar(30) NOT NULL,
			template_id mediumint(9) NOT NULL,
			PRIMARY KEY (published_form_id,template_type)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_templates (
			template_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			set_id mediumint(9) NOT NULL,
			template_type varchar(30) NOT NULL,
			template_name varchar(255) NOT NULL,
			content mediumtext,
			list_order smallint(6) NOT NULL,
			PRIMARY KEY (template_id)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_template_sets (
			set_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			set_name varchar(255) NOT NULL,
			version varchar(20) NOT NULL,
			description mediumtext,
			is_complete enum('yes','no') NOT NULL,
			list_order smallint(6) NOT NULL,
			PRIMARY KEY (set_id)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_template_set_placeholders (
			placeholder_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			set_id mediumint(9) NOT NULL,
			placeholder_label varchar(255) NOT NULL,
			placeholder varchar(255) NOT NULL,
			field_type enum('textbox','textarea','password','radios','checkboxes','select','multi-select') NOT NULL,
			field_orientation enum('horizontal','vertical','na') NOT NULL,
			default_value varchar(255) DEFAULT NULL,
			field_order smallint(6) NOT NULL,
			PRIMARY KEY (placeholder_id)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_template_set_placeholder_opts (
			placeholder_id mediumint(9) NOT NULL,
			option_text varchar(255) NOT NULL,
			field_order smallint(6) NOT NULL,
			PRIMARY KEY (placeholder_id,field_order)
		) DEFAULT CHARSET=utf8
	";

	$queries[] = "
		CREATE TABLE {$g_table_prefix}module_form_builder_template_set_resources (
			resource_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			resource_type enum('css','js') NOT NULL,
			template_set_id mediumint(8) unsigned NOT NULL,
			resource_name varchar(255) NOT NULL,
			placeholder varchar(100) NOT NULL,
			content mediumtext NOT NULL,
			last_updated datetime NOT NULL,
			list_order SMALLINT NOT NULL,
			PRIMARY KEY (resource_id)
		) DEFAULT CHARSET=utf8
	";

	foreach ($queries as $query)
	{
		$result = mysql_query($query);
		if (!$result)
		{
			$last_error = mysql_error();
			fb_delete_tables();
			return array(false, $LANG["form_builder"]["notify_installation_problem_c"] . " <b>$last_error</b>");
		}
	}

	// populate the database with the default template sets
	fb_populate_default_template_sets();

	// now add the settings
	$default_published_folder_path = $g_root_dir . "/modules/form_builder/published";
	$default_published_folder_url  = $g_root_url . "/modules/form_builder/published";
	$settings = array(
		"default_form_offline_page_content" => "<h2 class=\"ts_heading\">Sorry!</h2>\n\n<p>\n  The form is currently offline.\n</p>",
		"scheduled_offline_form_behaviour"  => "allow_completion",
		"default_thankyou_page_content"     => "<h2 class=\"ts_heading\">Thanks!</h2>\n\n<p>\n  Your form has been processed. Thanks for submitting the form.\n</p>\n\n<p>\n  <a href=\"?page=1\">Click here</a> to put through another submission.\n</p>",
		"default_published_folder_path"     => $default_published_folder_path,
		"default_published_folder_url"      => $default_published_folder_url,
		"review_page_title"                 => "Review",
		"thankyou_page_title"               => "Thankyou",
		"form_builder_width"                => 1000,
		"form_builder_height"               => 700,
		"phrase_edit_in_form_builder_link_action" => "same_window",
		"demo_mode"                         => "off"
	);
	ft_set_settings($settings, "form_builder");

	// initialize the hooks
	fb_reset_hooks();

	return array(true, "");
}


/**
 * This completely uninstalls the Form Builder module. Any forms marked as Form Builder forms will be changed
 * to Internal forms.
 */
function form_builder__uninstall($module_id)
{
	global $g_table_prefix;

	fb_delete_tables();

	@mysql_query("
		UPDATE {$g_table_prefix}forms
		SET    form_type = 'internal'
		WHERE  form_type = 'form_builder'
	");

	return array(true, "");
}


function form_builder__update($old_version_info, $new_version_info)
{
	global $g_table_prefix;

	$old_version_num = $old_version_info["version"];

	// normally we do a date comparison, but the dates got messed up in 1.0.5, so we have to do a workaround by
	// converting the version number to a number
	$old_version_int = preg_replace("/\D/", "", $old_version_num);
	if ($old_version_int <= 105)
	{
		$update_query = mysql_query("
			RENAME TABLE {$g_table_prefix}module_form_builder_template_set_placeholder_options
			TO {$g_table_prefix}module_form_builder_template_set_placeholder_opts
		");

		if (!$update_query)
		{
			return array(false, "There was a problem renaming your module_form_builder_template_set_placeholder_options table to module_form_builder_template_set_placeholder_opts.");
		}
	}

	fb_reset_hooks();
	return array(true, "");
}


/**
 * Called during installation in case there are problems, to roll back anyd tables that had been
 * created. Also called during uninstallation.
 */
function fb_delete_tables()
{
	global $g_table_prefix;

	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_forms");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_form_placeholders");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_form_templates");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_templates");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_template_sets");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_template_set_placeholders");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_template_set_placeholder_opts");
	@mysql_query("DROP TABLE {$g_table_prefix}module_form_builder_template_set_resources");
}


/**
 * Called on installation. This populates the database with the default template set data stored in /global/code/default_sets.php.
 * Eventually, this will be replaced with the Shared Resources.
 */
function fb_populate_default_template_sets()
{
	global $g_table_prefix;

	// not clear, but this includes the $g_default_sets "global"
	require_once(dirname(__FILE__) . "/default_sets.php");
	fb_import_template_set_data($g_default_sets);
}