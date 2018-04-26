<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\Fields;
use FormTools\Forms as CoreForms;
use FormTools\Views;
use FormTools\ViewTabs;
use Smarty, SmartyBC;


class General
{

    /**
     * Helper function to return a JS array of View IDs and the number of tabs in each.
     *
     * @param integer $form_id
     */
    public static function getNumViewTabsJs($form_id)
    {
        $view_ids = Views::getViewIds($form_id);
        $view_id_to_num_tabs = array();

        foreach ($view_ids as $view_id) {
            $tabs = ViewTabs::getViewTabs($view_id, true);
            $num_tabs = count($tabs);
            $view_id_to_num_tabs[] = "[$view_id,$num_tabs]";
        }

        $view_id_to_num_tab_str = implode(",", $view_id_to_num_tabs);

        return $view_id_to_num_tab_str;
    }


    public static function createNewSmartyInstance($delimiters = "double")
    {
        $root_dir = Core::getRootDir();

        if ($delimiters == "single") {
            $left_delimiter = "{";
            $right_delimiter = "}";
        } else {
            $left_delimiter = "{{";
            $right_delimiter = "}}";
        }

        if (method_exists(new Core(), "useSmartyBC")) {
            $smarty = Core::useSmartyBC() ? new SmartyBC() : new Smarty();
        } else {
            $smarty = new Smarty();
        }
        $smarty->setTemplateDir("$root_dir/themes/default");
        $smarty->setCompileDir("$root_dir/themes/default/cache/");
        $smarty->setUseSubDirs(Core::shouldUseSmartySubDirs());
        $smarty->left_delimiter = $left_delimiter;
        $smarty->right_delimiter = $right_delimiter;
        $smarty->addPluginsDir(array(
            "$root_dir/global/smarty_plugins",
            "$root_dir/modules/form_builder/smarty_plugins"
        ));

        return $smarty;
    }


    /**
     * Helper function to return a list of View tabs.
     *
     * @param array $view_info
     */
    public static function getViewTabsFromViewInfo($view_info)
    {
        $view_tabs = array();

        foreach ($view_info["tabs"] as $tab_info) {
            $tab_label = trim($tab_info["tab_label"]);
            if (!empty($tab_label)) {
                $view_tabs[] = array("tab_label" => $tab_label);
            }
        }

        if (empty($view_tabs)) {
            $view_tabs[] = array("tab_label" => "Form");
        }

        return $view_tabs;
    }


    /**
     * Creates a Form Builder form. Same as ft_create_internal_form(), except for the form type.
     *
     * @param $info array POST request containing the form name, number of fields and access type.
     */
    public static function createForm($request)
    {
        $db = Core::$db;
        $LANG = Core::$L;

        $rules = array();
        $rules[] = "required,form_name,{$LANG["validation_no_form_name"]}";
        $rules[] = "required,num_fields,{$LANG["validation_no_num_form_fields"]}";
        $rules[] = "digits_only,num_fields,{$LANG["validation_invalid_num_form_fields"]}";
        $rules[] = "required,access_type,{$LANG["validation_no_access_type"]}";

        $errors = validate_fields($request, $rules);
        if (!empty($errors)) {
            array_walk($errors, create_function('&$el','$el = "&bull;&nbsp; " . $el;'));
            $message = join("<br />", $errors);
            return array(false, $message);
        }

        $config = array(
            "form_type"    => "form_builder",
            "form_name"    => $request["form_name"],
            "access_type"  => $request["access_type"],
            "submission_type" => "code"
        );

        // set up the entry for the form
        list($success, $message, $new_form_id) = CoreForms::setupForm($config);

        $form_data = array(
            "form_tools_form_id" => $new_form_id,
            "form_tools_display_notification_page" => false
        );

        for ($i=1; $i<=$request["num_fields"]; $i++) {
            $form_data["field{$i}"] = $i;
        }
        CoreForms::initializeForm($form_data);

        $form_fields = Fields::getFormFields($new_form_id);

        $order = 1;

        // if the user just added a form with a lot of fields (over 50), the database row size will be too
        // large. Varchar fields (which with utf-8 equates to 1220 bytes) in a table can have a combined row
        // size of 65,535 bytes, so 53 is the max. The client-side validation limits the number of fields to
        // 1000. Any more will throw an error.
        $field_size_clause = ($request["num_fields"] > 50) ? ", field_size = 'small'" : "";

        foreach ($form_fields as $field_info) {
            if (preg_match("/field(\d+)/", $field_info["field_name"], $matches)) {
                $db->query("
                    UPDATE {PREFIX}form_fields
                    SET    field_title = :field_title,
                           col_name = :col_name
                           $field_size_clause
                    WHERE  field_id = :field_id
                ");
                $db->bindAll(array(
                    "field_title" => "{$LANG["word_field"]} $order",
                    "col_name" => "col_$order",
                    "field_id" => $field_info["field_id"]
                ));
                $db->execute();
                $order++;
            }
        }

        CoreForms::finalizeForm($new_form_id);

        // if the form has an access type of "private" add whatever client accounts the user selected
        if ($request["access_type"] == "private") {
            $selected_client_ids = $request["selected_client_ids"];
            $queries = array();

            foreach ($selected_client_ids as $client_id) {
                $queries[] = "($client_id, $new_form_id)";
            }

            if (!empty($queries)) {
                $insert_values = implode(",", $queries);
                $db->query("
                    INSERT INTO {PREFIX}client_forms (account_id, form_id)
                    VALUES $insert_values
                ");
            }
        }

        // now apply a few simple changes to the View we just created, to simplify things for the
        $views = Views::getFormViews($new_form_id);
        $view_id = $views[0]["view_id"];

        // 1. Change the View name to "Form Builder View"
        $db->query("UPDATE {PREFIX}views SET view_name = 'Form Builder View' WHERE view_id = :view_id");
        $db->bind("view_id", $view_id);
        $db->execute();

        // 2. Change the View's first tab (the only one defined!) to be called "Page 1"
        $db->query("UPDATE {PREFIX}view_tabs SET tab_label = 'Page 1' WHERE view_id = :view_id AND tab_number = 1 LIMIT 1");
        $db->bind("view_id", $view_id);
        $db->execute();

        // 3. Change the View Field Group label to "Fields" instead of "DATA"
        $db->query("UPDATE {PREFIX}list_groups SET group_name = 'Fields' WHERE group_type = :group_type LIMIT 1");
        $db->bind("group_type", "view_fields_{$view_id}");
        $db->execute();

        return array(true, $LANG["notify_internal_form_created"], $new_form_id);
    }


    /**
     * What a beautiful function name. This largely duplicates some Core JS code, but it's necessary + a good idea for later on when
     * we want to offer different ways to display validation errors.
     *
     * @return string
     */
    public static function getFormValidationCustomErrorHandlerJs()
    {
        $LANG = Core::$L;

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
}
