<?php

namespace FormTools\Modules\FormBuilder;

use FormTools\Core;
use FormTools\Module as CoreModule;
use FormTools\Schemas;

use PDO, Exception;

class Module extends CoreModule
{
    protected $moduleName = "Form Builder";
    protected $moduleDesc = "Publish any Form Tools form to make it publicly available on your website.";
    protected $author = "Ben Keen";
    protected $authorEmail = "ben.keen@gmail.com";
    protected $authorLink = "https://formtools.org";
    protected $version = "2.0.0";
    protected $date = "2017-11-18";
    protected $originLanguage = "en_us";
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
                    offline_date datetime NOT NULL,
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

        Hooks::resetHooks();

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

        while (($file = readdir($dh)) !== false) {
            $parts = pathinfo($file);
            if ($parts["extension"] !== "json") {
                continue;
            }

            $template_set = json_decode(file_get_contents("$data_folder/$file"));
            $schema = json_decode(file_get_contents("$root_dir/modules/form_builder/schemas/template_set-1.0.0.json"));
            $response = Schemas::validateSchema($template_set, $schema);

            if ($response["is_valid"]) {
                TemplateSets::importTemplateSetData($template_set);
            } else {
                // TODO
            }
        }
    }

}


