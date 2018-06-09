<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\General as CoreGeneral;
use FormTools\Hooks as CoreHooks;
use FormTools\Module as CoreModule;
use FormTools\Modules;
use FormTools\Schemas;

use Exception;

class Module extends CoreModule
{
    protected $moduleName = "Form Builder";
    protected $moduleDesc = "Publish any Form Tools form to make it publicly available on your website.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "https://formtools.org";
    protected $version = "2.0.10";
    protected $date = "2018-06-09";
    protected $originLanguage = "en_us";

    // important! This needs to be updated any time the default template set filename changes
    protected $defaultTemplateSet = "default-1.2.json";

    protected $jsFiles = array(
        "{MODULEROOT}/scripts/manage_template_sets.js",
        "{FTROOT}/global/scripts/sortable.js",
        "{FTROOT}/global/codemirror/lib/codemirror.js",
        "{FTROOT}/global/codemirror/mode/xml/xml.js",
        "{FTROOT}/global/codemirror/mode/smarty/smarty.js",
        "{FTROOT}/global/codemirror/mode/htmlmixed/htmlmixed.js",
        "{FTROOT}/global/codemirror/mode/css/css.js",
        "{FTROOT}/global/codemirror/mode/javascript/javascript.js",
        "{FTROOT}/global/codemirror/mode/clike/clike.js",
    );
    protected $cssFiles = array(
        "{MODULEROOT}/css/styles.css",
        "{FTROOT}/global/codemirror/lib/codemirror.css"
    );
    protected $nav = array(
        "phrase_template_sets" => array("index.php", false),
        "word_settings"        => array("settings.php", false),
        "word_help"            => array("help.php", false)
    );

    public function install($module_id)
    {
        $db = Core::$db;
        $root_dir = Core::getRootDir();
        $root_url = Core::getRootUrl();

        try {
            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_forms (
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
                    offline_date datetime DEFAULT NULL,
                    list_order smallint(6) NOT NULL,
                    PRIMARY KEY (published_form_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_form_placeholders (
                    published_form_id mediumint(9) NOT NULL,
                    placeholder_id mediumint(9) NOT NULL,
                    placeholder_value mediumtext NOT NULL,
                    UNIQUE KEY published_form_id (published_form_id, placeholder_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_form_templates (
                    published_form_id mediumint(9) NOT NULL,
                    template_type varchar(30) NOT NULL,
                    template_id mediumint(9) NOT NULL,
                    PRIMARY KEY (published_form_id,template_type)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_templates (
                    template_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    set_id mediumint(9) NOT NULL,
                    template_type varchar(30) NOT NULL,
                    template_name varchar(255) NOT NULL,
                    content mediumtext,
                    list_order smallint(6) NOT NULL,
                    PRIMARY KEY (template_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_template_sets (
                    set_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    set_name varchar(255) NOT NULL,
                    version varchar(20) NOT NULL,
                    description mediumtext,
                    is_complete enum('yes','no') NOT NULL,
                    list_order smallint(6) NOT NULL,
                    PRIMARY KEY (set_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_template_set_placeholders (
                    placeholder_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    set_id mediumint(9) NOT NULL,
                    placeholder_label varchar(255) NOT NULL,
                    placeholder varchar(255) NOT NULL,
                    field_type enum('textbox','textarea','password','radios','checkboxes','select','multi-select') NOT NULL,
                    field_orientation enum('horizontal','vertical','na') NOT NULL,
                    default_value varchar(255) DEFAULT NULL,
                    field_order smallint(6) NOT NULL,
                    PRIMARY KEY (placeholder_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_template_set_placeholder_opts (
                    placeholder_id mediumint(9) NOT NULL,
                    option_text varchar(255) NOT NULL,
                    field_order smallint(6) NOT NULL,
                    PRIMARY KEY (placeholder_id,field_order)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();

            $db->query("
                CREATE TABLE {PREFIX}module_form_builder_template_set_resources (
                    resource_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
                    resource_type enum('css','js') NOT NULL,
                    template_set_id mediumint(8) unsigned NOT NULL,
                    resource_name varchar(255) NOT NULL,
                    placeholder varchar(100) NOT NULL,
                    content mediumtext NOT NULL,
                    last_updated datetime NOT NULL,
                    list_order SMALLINT NOT NULL,
                    PRIMARY KEY (resource_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $db->execute();
        } catch (Exception $e) {
            $this->deleteTables();
            $L = $this->getLangStrings();
            return array(false, $L["notify_installation_problem_c"] . " <b>" . $e->getMessage() . "</b>");
        }

        // populate the database with the default template sets
        list ($template_sets_installed, $template_set_install_error) = $this->populateDefaultTemplateSets();

        $this->setSettings(array(
            "default_form_offline_page_content" => "<h2 class=\"ts_heading\">Sorry!</h2>\n\n<p>\n  The form is currently offline.\n</p>",
            "scheduled_offline_form_behaviour"  => "allow_completion",
            "default_thankyou_page_content"     => "<h2 class=\"ts_heading\">Thanks!</h2>\n\n<p>\n  Your form has been processed. Thanks for submitting the form.\n</p>\n\n<p>\n  <a href=\"?page=1\">Click here</a> to put through another submission.\n</p>",
            "default_published_folder_path"     => "$root_dir/modules/form_builder/published",
            "default_published_folder_url"      => "$root_url/modules/form_builder/published",
            "review_page_title"                 => "Review",
            "thankyou_page_title"               => "Thankyou",
            "form_builder_width"                => 1000,
            "form_builder_height"               => 700,
            "edit_form_builder_link_action"     => "same_window",
            "demo_mode"                         => "off"
        ));

        $this->resetHooks();

        return array(true, "");
    }


    /**
     * Any forms marked as Form Builder forms will be changed to Internal forms after installation.
     */
    public function uninstall($module_id)
    {
        $db = Core::$db;
        $this->deleteTables();

        $db->query("
            UPDATE {PREFIX}forms
            SET    form_type = 'internal'
            WHERE  form_type = 'form_builder'
        ");
        $db->execute();

        return array(true, "");
    }


    public function upgrade($module_id, $old_module_version)
    {
        $this->resetHooks();

        if (CoreGeneral::isVersionEarlierThan($old_module_version, "2.0.10")) {
            $this->updateOfflineDateFieldAllowNulls();
        }
    }


    public function resetHooks()
    {
        CoreHooks::unregisterModuleHooks("form_builder");

        CoreHooks::registerHook("template", "form_builder", "add_form_page", "", "displayAddFormOption", 50, true);
        CoreHooks::registerHook("template", "form_builder", "admin_edit_form_main_tab_form_type_dropdown", "", "displayFormTypeOption", 50, true);
        CoreHooks::registerHook("template", "form_builder", "admin_forms_form_type_label", "", "displayFormBuilderLabel", 50, true);
        CoreHooks::registerHook("template", "form_builder", "admin_edit_form_content", "", "displayPublishTab", 50, true);

        CoreHooks::registerHook("code", "form_builder", "start", "FormTools\\Modules::moduleOverrideData", "inlineDataOverride", 50, true);
        CoreHooks::registerHook("code", "form_builder", "end", "FormTools\\General::displayCustomPageMessage", "displayFormCreatedMessage", 50, true);
        CoreHooks::registerHook("code", "form_builder", "start", "FormTools\\Forms::deleteForm", "onDeleteForm", 50);
        CoreHooks::registerHook("code", "form_builder", "end", "FormTools\\Views::deleteView", "onDeleteView", 50);
    }


    /**
     * Called during installation in case there are problems, to roll back anyd tables that had been
     * created. Also called during uninstallation.
     */
    public function deleteTables()
    {
        $db = Core::$db;

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_forms");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_form_placeholders");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_form_templates");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_templates");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_template_sets");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_template_set_placeholders");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_template_set_placeholder_opts");
        $db->execute();

        $db->query("DROP TABLE IF EXISTS {PREFIX}module_form_builder_template_set_resources");
        $db->execute();
    }


    /**
     * Called on installation. This populates the database with the default template sets found in JSON format in the
     * default_template_sets folder.
     */
    public function populateDefaultTemplateSets()
    {
        $root_dir = Core::getRootDir();

        $data_folder = "$root_dir/modules/form_builder/default_template_sets";
        $dh = opendir($data_folder);

        if (!$dh) {
            return array(false, "You appear to be missing the default_template_sets folder, or your \$g_root_dir settings is invalid.");
        }

        $template_set_files = array();
        while (($file = readdir($dh)) !== false) {
            $parts = pathinfo($file);
            if ($parts["extension"] !== "json") {
                continue;
            }

            $template_set = json_decode(file_get_contents("$data_folder/$file"));
            $schema = json_decode(file_get_contents("$root_dir/modules/form_builder/schemas/template_set-1.0.0.json"));
            $response = Schemas::validateSchema($template_set, $schema);

            if ($response["is_valid"]) {
                $template_set_files[$file] = $template_set;
            } else {
                // TODO
            }
        }

        // now install the template sets. Ensure the "default-*.json" one is set first
        TemplateSets::importTemplateSetData($template_set_files[$this->defaultTemplateSet]);

        foreach ($template_set_files as $filename => $template_set) {
            if ($filename === $this->defaultTemplateSet) {
                continue;
            }
            TemplateSets::importTemplateSetData($template_set);
        }
    }
    

    /**
     * This adds the "Form Builder" section on the Add Form page.
     */
    public function displayAddFormOption() {
        $L = $this->getLangStrings();

        $LANG = Core::$L;
        $root_url = Core::getRootUrl();

        $select = mb_strtoupper($LANG["word_select"]);

        echo <<< END
    <table width="100%">
      <tr>
        <td width="49%" valign="top">
          <div class="grey_box add_form_select">
            <span style="float:right">
              <input type="button" name="form_builder" class="blue bold" value="$select"
                onclick="window.location='$root_url/modules/form_builder/admin/add_form.php'" />
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
    public function displayFormTypeOption($location, $vars) {
        $form_type = $vars["form_info"]["form_type"];
        $L = $this->getLangStrings();

        $selected = ($form_type == "form_builder") ? "selected=\"selected\"" : "";
        echo "<option value=\"form_builder\" $selected>{$L["module_name"]}</option>";
    }


    /**
     * Called after the user creates a new Form Builder form. It returns a custom message to display in the page.
     *
     * @param array $info
     */
    public function displayFormCreatedMessage($info)
    {
        $L = $this->getLangStrings();

        $flag = $info["flag"];

        if ($flag != "notify_form_builder_form_created") {
            return;
        }

        $message =<<< END
{$L["notify_form_builder_form_created"]}
<ul style="margin-bottom: 0px">
  <li><a href="https://docs.formtools.org/modules/form_builder/usage/tutorials/" target="_blank">{$L["phrase_quick_intro"]}</a></li>
  <li><a href="https://docs.formtools.org/modules/form_builder/" target="_blank">{$L["phrase_form_builder_doc"]}</a></li>
</ul>
END;

        return array(
            "found" => true,
            "g_success" => true,
            "g_message" => $message
        );
    }


    /**
     * Used to render the Publish tab on the Edit Form pages.
     *
     * @param string $location
     * @param array $vars
     */
    public function displayPublishTab($location, $vars)
    {
        $root_dir = Core::getRootDir();
        $LANG = Core::$L;
        $module = Modules::getModuleInstance("form_builder");
        $L = $module->getLangStrings();

        $form_id = $vars["form_info"]["form_id"];
        $published_forms = Forms::getPublishedForms($form_id);

        // loop through each published form and take any offline that need it
        $at_least_one_form_just_taken_offline = false; // yes, this variable name ROCKS!!!!!
        foreach ($published_forms["results"] as $config) {
            if ($config["is_online"] == "yes" && !is_null($config["offline_date"])) {
                $taken_offline = FormGenerator::maybeTakeScheduledFormOffline($config);
                if ($taken_offline) {
                    $at_least_one_form_just_taken_offline = true;
                }
            }
        }

        // if one of the forms was just taken offline, re-request the published form list so they'll show as online = "no" in the UI
        // on this page load
        if ($at_least_one_form_just_taken_offline) {
            $published_forms = Forms::getPublishedForms($form_id);
        }

        $demo_mode = $this->getSettings("demo_mode");

        $form_type = ucwords($vars["form_info"]["form_type"]);
        $text_non_form_builder_form = CoreGeneral::evalSmartyString($L["text_non_form_builder_form"], array("form_type" => $form_type));

        $smarty = General::createNewSmartyInstance("single");
        $smarty->assign("L", $L);
        $smarty->assign("LANG", $LANG);
        $smarty->assign("form_id", $form_id);
        $smarty->assign("form_info", $vars["form_info"]);
        $smarty->assign("published_forms", $published_forms);
        $smarty->assign("demo_mode", $demo_mode);
        $smarty->assign("text_non_form_builder_form", $text_non_form_builder_form);
        $smarty->assign("same_page", CoreGeneral::getCleanPhpSelf());

        $output = $smarty->fetch("$root_dir/modules/form_builder/templates/admin/tab_publish.tpl");

        echo $output;
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
    public function displayFormBuilderLabel($location, $vars)
    {
        $L = $this->getLangStrings();
        $curr_form_info = $vars["form_info"]; // the form in the current loop
        if ($curr_form_info["form_type"] == "form_builder") {
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
    public static function inlineDataOverride($vars)
    {
        $module_id = Modules::getModuleIdFromModuleFolder("form_builder");
        $module_info = Modules::getModule($module_id);
        if ($module_info["is_installed"] != "yes" && $module_info["is_enabled"] != "yes") {
            return;
        }

        $module = Modules::getModuleInstance("form_builder");
        $L = $module->getLangStrings();

        switch ($vars["location"]) {
            // this adds the "Publish" tab to the Edit Form pages
            case "admin_edit_form_tabs":
                $tabs = $vars["data"];
                $tabs["publish"] = array(
                    "tab_label" => $L["word_publish"],
                    "tab_link"  => "index.php?page=publish",
                    "pages"     => array("publish")
                );
                return array("data" => $tabs);
                break;

            // this ensures the right code page is called when the user clicks on the Publish tab
            case "admin_edit_form_page_name_include":
                $request = array_merge($_POST, $_GET);
                if (isset($request["page"]) && $request["page"] == "publish") {
                    $file = realpath(__DIR__ . "/../admin/tab_publish.php");
                    return array("data" => array("page_name" => $file));
                }
                break;
        }
    }


    /**
     * This deletes all form configurations and published forms when a form is deleted.
     *
     * @param array $info
     */
    public function onDeleteForm($info)
    {
        $form_id = $info["form_id"];
        $L = $this->getLangStrings();

        $published_forms = Forms::getPublishedForms($form_id);
        foreach ($published_forms["results"] as $config)
        {
            $published_form_id = $config["published_form_id"];
            list($success, $message) = Forms::deletePublishedForm($form_id, $published_form_id, "yes", $L);

            // if there was a problem with the last function call, there was probably just a problem deleting
            // one of the files. Ignore this: just re-call the function with override "on". This ensures the configuration
            // is at least deleted
            if (!$success) {
                Forms::deletePublishedForm($form_id, $published_form_id, "yes", $L, true);
            }
        }
    }

    public function deletePublishedForm($form_id, $published_form_id, $delete_form_config, $L, $override)
    {
        return Forms::deletePublishedForm($form_id, $published_form_id, $delete_form_config, $L, $override);
    }

    /**
     * This is called whenever the administrator deletes a View. It checks to see if the View is being used for a published form.
     * If it IS, it deletes that published form + configuration.
     *
     * @param array $info
     */
    public function onDeleteView($info)
    {
        $db = Core::$db;
        $L = $this->getLangStrings();

        if (!isset($info["view_id"]) || !is_numeric($info["view_id"])) {
            return;
        }

        $db->query("
            SELECT published_form_id, form_id
            FROM   {PREFIX}module_form_builder_forms
            WHERE  view_id = :view_id
        ");
        $db->bind("view_id", $info["view_id"]);
        $db->execute();

        $rows = $db->fetchAll();
        foreach ($rows as $row) {
            $published_form_id = $row["published_form_id"];
            $form_id           = $row["form_id"];

            // always attempt to delete the published form as well as the config first. If that fails, just delete the configuration
            list($success, $message) = Forms::deletePublishedForm($form_id, $published_form_id, "yes", $L);
            if (!$success) {
                Forms::deletePublishedForm($form_id, $published_form_id, "yes", $L, true);
            }
        }
    }

    private function updateOfflineDateFieldAllowNulls()
    {
        $db = Core::$db;
        $db->query("ALTER TABLE {PREFIX}module_form_builder_forms CHANGE offline_date offline_date DATETIME NULL");
        $db->execute();

        $db->query("
            UPDATE {PREFIX}module_form_builder_forms
            SET offline_date = NULL
            WHERE offline_date = '0000-00-00 00:00:00'
        ");
        $db->execute();
    }
}
