<?php

$HOOKS = array(
    array(
        "hook_type" => "template",
        "action_location" => "add_form_page",
        "function_name" => "",
        "hook_function" => "displayAddFormOption",
        "priority" => "50"
    ),
    array(
        "hook_type" => "template",
        "action_location" => "admin_edit_form_main_tab_form_type_dropdown",
        "function_name" => "",
        "hook_function" => "displayFormTypeOption",
        "priority" => "50"
    ),
    array(
        "hook_type" => "template",
        "action_location" => "admin_forms_form_type_label",
        "function_name" => "",
        "hook_function" => "displayFormBuilderLabel",
        "priority" => "50"
    ),
    array(
        "hook_type" => "code",
        "action_location" => "start",
        "function_name" => "FormTools\\Modules::moduleOverrideData",
        "hook_function" => "inlineDataOverride",
        "priority" => "50"
    ),
    array(
        "hook_type" => "code",
        "action_location" => "end",
        "function_name" => "FormTools\\General::displayCustomPageMessage",
        "hook_function" => "displayFormCreatedMessage",
        "priority" => "50"
    ),
    array(
        "hook_type" => "template",
        "action_location" => "admin_edit_form_content",
        "function_name" => "",
        "hook_function" => "displayPublishTab",
        "priority" => "50"
    ),
    array(
        "hook_type" => "code",
        "action_location" => "start",
        "function_name" => "FormTools\\Forms::deleteForm",
        "hook_function" => "onDeleteForm",
        "priority" => "50"
    ),
    array(
        "hook_type" => "code",
        "action_location" => "end",
        "function_name" => "FormTools\\Views::deleteView",
        "hook_function" => "onDeleteView",
        "priority" => "50"
    )
);

$FILES = array(
    "admin/",
    "admin/add_form.php",
    "admin/tab_publish.php",
    "code/",
    "code/actions.php",
    "code/FormGenerator.class.php",
    "code/Forms.class.php",
    "code/General.class.php",
    "code/index.html",
    "code/Module.class.php",
    "code/Placeholders.class.php",
    "code/Resources.class.php",
    "code/Templates.class.php",
    "code/TemplateSets.class.php",
    "css/",
    "css/builder.css",
    "css/codemirror.css",
    "css/edit_form.css",
    "css/index.html",
    "css/styles.css",
    "default_template_sets/",
    "default_template_sets/conformist-1.2.json",
    "default_template_sets/default-1.2.json",
    "default_template_sets/illuminate-1.1.json",
    "default_template_sets/prosimple-1.3.json",
    "form_resources/",
    "form_resources/css.php",
    "form_resources/js.php",
    "images/",
    "images/builder_logo.png",
    "images/dialog_header_bg.png",
    "images/dialog_option.png",
    "images/dialog_selected_option.png",
    "images/icon16.png",
    "images/icon24.png",
    "images/icon26.png",
    "images/icon_form_builder.png",
    "images/loading.gif",
    "images/sidebar_section_loading.gif",
    "images/tip.png",
    "lang/",
    "lang/en_us.php",
    "lang/index.html",
    "published/",
    "schemas/",
    "schemas/README.md",
    "schemas/template_set-1.0.0.json",
    "scripts/",
    "scripts/builder.js",
    "scripts/index.html",
    "scripts/manage_forms.js",
    "scripts/manage_template_sets.js",
    "share/",
    "smarty_plugins/",
    "smarty_plugins/eval.tpl",
    "smarty_plugins/function.captcha.php",
    "smarty_plugins/function.code_block.php",
    "smarty_plugins/function.continue_block.php",
    "smarty_plugins/function.display_placeholder_field_type.php",
    "smarty_plugins/function.display_template_set_placeholders.php",
    "smarty_plugins/function.display_template_set_templates.php",
    "smarty_plugins/function.display_template_set_type.php",
    "smarty_plugins/function.display_template_set_usage.php",
    "smarty_plugins/function.display_template_type.php",
    "smarty_plugins/function.display_template_usage.php",
    "smarty_plugins/function.error_message.php",
    "smarty_plugins/function.footer.php",
    "smarty_plugins/function.header.php",
    "smarty_plugins/function.navigation.php",
    "smarty_plugins/function.page.php",
    "smarty_plugins/function.template_sets.php",
    "smarty_plugins/function.template_type_dropdown.php",
    "smarty_plugins/function.template_types.php",
    "smarty_plugins/modifier.in.php",
    "smarty_plugins/placeholders_html.tpl",
    "smarty_plugins/templates_html.tpl",
    "template_sets/",
    "template_sets/index.php",
    "template_sets/tab_add_placeholder.php",
    "template_sets/tab_edit_placeholder.php",
    "template_sets/tab_edit_resource.php",
    "template_sets/tab_edit_template.php",
    "template_sets/tab_info.php",
    "template_sets/tab_placeholders.php",
    "template_sets/tab_resources.php",
    "template_sets/tab_templates.php",
    "templates/",
    "templates/admin/",
    "templates/admin/add_form.tpl",
    "templates/admin/tab_publish.tpl",
    "templates/help.tpl",
    "templates/index.html",
    "templates/index.tpl",
    "templates/preview.tpl",
    "templates/settings.tpl",
    "templates/tab_settings_form_offline.tpl",
    "templates/tab_settings_main.tpl",
    "templates/tab_settings_thanks.tpl",
    "templates/template_sets/",
    "templates/template_sets/index.tpl",
    "templates/template_sets/tab_add_placeholder.tpl",
    "templates/template_sets/tab_edit_placeholder.tpl",
    "templates/template_sets/tab_edit_resource.tpl",
    "templates/template_sets/tab_edit_template.tpl",
    "templates/template_sets/tab_info.tpl",
    "templates/template_sets/tab_placeholders.tpl",
    "templates/template_sets/tab_resources.tpl",
    "templates/template_sets/tab_templates.tpl",
    "form.php",
    "help.php",
    "index.php",
    "library.php",
    "module_config.php",
    "preview.php",
    "preview_form.php",
    "README.md",
    "settings.php",
    "tab_settings_form_offline.php",
    "tab_settings_main.php",
    "tab_settings_thanks.php"
);

$STRUCTURE = array();
$STRUCTURE["tables"] = array();
$STRUCTURE["tables"]["module_form_builder_forms"] = array(
    array(
    "Field" => "published_form_id",
    "Type" => "mediumint(8) unsigned",
    "Null" => "NO",
    "Key" => "PRI",
    "Default" => ""
    ),
    array(
    "Field" => "is_online",
    "Type" => "enum('yes','no')",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "is_published",
    "Type" => "enum('yes','no')",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "form_id",
    "Type" => "mediumint(9)",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "view_id",
    "Type" => "mediumint(9)",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "set_id",
    "Type" => "mediumint(9)",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "publish_date",
    "Type" => "datetime",
    "Null" => "YES",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "filename",
    "Type" => "varchar(255)",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
    "Field" => "folder_path",
    "Type" => "mediumtext",
    "Null" => "NO",
    "Key" => "",
    "Default" => ""
    ),
    array(
"Field" => "folder_url",
"Type" => "mediumtext",
"Null" => "NO",
"Key" => "",
"Default" => ""
    ),
    array(
        "Field" => "include_review_page",
        "Type" => "enum('yes','no')",
        "Null" => "NO",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "include_thanks_page_in_nav",
        "Type" => "enum('yes','no')",
        "Null" => "NO",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "thankyou_page_content",
        "Type" => "mediumtext",
        "Null" => "YES",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "form_offline_page_content",
        "Type" => "mediumtext",
        "Null" => "YES",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "review_page_title",
        "Type" => "varchar(255)",
        "Null" => "YES",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "thankyou_page_title",
        "Type" => "varchar(255)",
        "Null" => "YES",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "offline_date",
        "Type" => "datetime",
        "Null" => "NO",
        "Key" => "",
        "Default" => ""
    ),
    array(
        "Field" => "list_order",
        "Type" => "smallint(6)",
        "Null" => "NO",
        "Key" => "",
        "Default" => ""
    )
);
$STRUCTURE["tables"]["module_form_builder_form_placeholders"] = array(
array(
"Field" => "published_form_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "placeholder_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "placeholder_value",
"Type" => "mediumtext",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_form_templates"] = array(
array(
"Field" => "published_form_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "template_type",
"Type" => "varchar(30)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "template_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_templates"] = array(
array(
"Field" => "template_id",
"Type" => "mediumint(8) unsigned",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "set_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "template_type",
"Type" => "varchar(30)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "template_name",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "content",
"Type" => "mediumtext",
"Null" => "YES",
"Key" => "",
"Default" => ""
),
array(
"Field" => "list_order",
"Type" => "smallint(6)",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_template_sets"] = array(
array(
"Field" => "set_id",
"Type" => "mediumint(8) unsigned",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "set_name",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "version",
"Type" => "varchar(20)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "description",
"Type" => "mediumtext",
"Null" => "YES",
"Key" => "",
"Default" => ""
),
array(
"Field" => "is_complete",
"Type" => "enum('yes','no')",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "list_order",
"Type" => "smallint(6)",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_template_set_placeholders"] = array(
array(
"Field" => "placeholder_id",
"Type" => "mediumint(8) unsigned",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "set_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "placeholder_label",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "placeholder",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "field_type",
"Type" => "enum('textbox','textarea','password','radios','checkboxes','select','multi-select')",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "field_orientation",
"Type" => "enum('horizontal','vertical','na')",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "default_value",
"Type" => "varchar(255)",
"Null" => "YES",
"Key" => "",
"Default" => ""
),
array(
"Field" => "field_order",
"Type" => "smallint(6)",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_template_set_placeholder_opts"] = array(
array(
"Field" => "placeholder_id",
"Type" => "mediumint(9)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "option_text",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "field_order",
"Type" => "smallint(6)",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
)
);
$STRUCTURE["tables"]["module_form_builder_template_set_resources"] = array(
array(
"Field" => "resource_id",
"Type" => "mediumint(8) unsigned",
"Null" => "NO",
"Key" => "PRI",
"Default" => ""
),
array(
"Field" => "resource_type",
"Type" => "enum('css','js')",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "template_set_id",
"Type" => "mediumint(8) unsigned",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "resource_name",
"Type" => "varchar(255)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "placeholder",
"Type" => "varchar(100)",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "content",
"Type" => "mediumtext",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "last_updated",
"Type" => "datetime",
"Null" => "NO",
"Key" => "",
"Default" => ""
),
array(
"Field" => "list_order",
"Type" => "smallint(6)",
"Null" => "NO",
"Key" => "",
"Default" => ""
)
);
