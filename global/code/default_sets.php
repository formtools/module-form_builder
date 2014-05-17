<?php

/**
 * This page contains a PHP representation of all the default template sets, used during installation.
 * *Ideally*, this wouldn't be needed: once the Shared Resources module + website is complete, we'll drop
 * this altogether and rely exlusively on that module to import the initial template sets. However,
 * (a) it's not ready yet and (b) it will require a few things that the user may not have (PHP5, Curl,
 * SimpleXML).
 *
 * So in the meantime....
 */


$g_default_sets = array();

$g_default_sets[] = array(
  "set_name"    => "Default Template Set",
  "version"     => "1.0",
  "description" => "A neutral, grey-themed set of templates that lets you choose a highlight colour to match your site. ",
  "is_complete" => "yes",
  "list_order"  => 1,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:900px\">\n    <div class=\"ts_header\">\n      <h1>{{\$form_name}}</h1>\n    </div>\n    <div class=\"ts_content\">\n      <div class=\"ts_content_inner\">\n"
    ),
    array(
      "template_type" => "header",
      "template_name" => "No Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:900px\">\n    <div class=\"ts_content\">\n      <div class=\"ts_content_inner\">\n"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "    </div> <!-- ends class=\"ts_content_inner\" div -->\n  </div> <!-- ends class=\"ts_content\" div -->\n</div> <!-- ends class=\"ts_page\" div -->\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "{{navigation}}\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n    <a name=\"s{{\$group.group_id}}\"></a>\n  {{if \$group.group_name}}\n    <h3>{{\$group.group_name}}</h3>\n  {{else}}\n    <br />\n  {{/if}}\n\n  {{if \$fields|@count > 0}}\n  <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n  {{/if}}\n    \n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"180\" valign=\"top\">\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </td>\n      <td class=\"answer\" valign=\"top\">\n        <div class=\"pad_left\">\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n        </div>\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>  \n  {{/if}}\n\n{{/foreach}}\n\n{{continue_block}}\n\n</form>"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "{{navigation}}\n\n<h2>{{\$review_page_title}}</h2>\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n{{foreach from=\$grouped_fields item=curr_group}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  {{if \$fields|@count > 0}}\n    <h3>\n      <a href=\"?page={{\$group.custom_data}}#s{{\$group.group_id}}\">EDIT</a>\n      {{\$group.group_name}}\n    </h3>\n  \n    <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"200\" class=\"pad_left_small\" valign=\"top\">{{\$curr_field.field_title}}</td>\n      <td class=\"answer\" valign=\"top\">\n        <div class=\"pad_left\">\n        {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n          field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n        </div>\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>    \n  {{/if}}\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "{{navigation}}\n\n{{\$thankyou_page_content}}\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "{{\$form_offline_page_content}}"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Button Only",
      "content"       => "<div class=\"ts_continue_button\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Detailed",
      "content"       => "<div class=\"ts_continue_block\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n  This is page <b>{{\$current_page}}</b> of <b>{{\$num_pages}}</b>. You must complete \n  all steps in order for your submission to be processed. Please click continue.\n</div>\n\n"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Simple",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li><a href=\"?page={{\$i}}\">{{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$page_info.page_name}}</li>\n    {{else}}\n      <li>{{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Numbered",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li><a href=\"{{\$filename}}?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$i}}. {{\$page_info.page_name}}</li>\n    {{else}}\n      <li>{{\$i}}. {{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "No Navigation",
      "content"       => "\n"
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"fb_error\">{{\$validation_error}}</div>\n{{/if}}\n\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "General Styles",
      "placeholder"   => "styles",
      "content"       => "body {\n  text-align: center;\n  padding: 0px;\n  margin: 0px;\n  background-color: #efefef;\n}\ntd, th, p, input, textarea, select,ul,li,div, span {\n  font-family: \"Lucida Grande\",\"Lucida Sans Unicode\", Tahoma, sans-serif;\n  font-size: 12px;\n  color: #555555;\n}\ntd, th, p, textarea, ul,li, div {\n  line-height: 22px;\n}\na:link, a:visited {\n  color: #336699;\n}\ntable {\n  empty-cells: show;\n}\n\n/* page sections */\n.ts_page:after {\n  -moz-transform: translate(0pt, 0pt);\n  background: none repeat scroll 0 0 transparent;\n  border-radius: 20px 20px 20px 20px;\n  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);\n  content: \"\";\n  left: 0;\n  position: absolute;\n  width: 100%;\n  z-index: -2;\n}\n.ts_page {\n  margin: 40px auto;\n  position: relative;\n  text-align: left;\n}\n.ts_header {\n  background: none repeat scroll 0 0 rgba(0, 0, 0, 0.5);\n  border: 3px solid #CCCCCC;\n  height: 140px;\n  background: #3a3a3a; /* Old browsers */\n  background: -moz-linear-gradient(45deg,  #777777 0%, #999999 100%); /* FF3.6+ */\n  background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#777777), color-stop(100%,#999999)); /* Chrome,Safari4+ */\n  background: -webkit-linear-gradient(45deg,  #777777 0%,#999999 100%); /* Chrome10+,Safari5.1+ */\n  background: -o-linear-gradient(45deg,  #777777 0%,#999999 100%); /* Opera 11.10+ */\n  background: -ms-linear-gradient(45deg,  #777777 0%,#999999 100%); /* IE10+ */\n  background: linear-gradient(45deg,  #777777 0%,#999999 100%); /* W3C */\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#777777', endColorstr='#999999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */\n  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);\n}\n.ts_header h1 {\n  margin: 56px 50px;\n  padding: 0px;\n  font-size: 20px;\n  color: white;\n}\n.ts_content {\n  background-color: white;\n  border: 1px solid #CCCCCC;\n  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);\n  padding: 25px 50px;\n}\n.ts_continue_block {\n  margin-top: 16px;\n  background-color: #ffffdd;\n  padding: 8px;\n  box-shadow:1px 2px 2px #878787;\n}\n.ts_continue_block input {\n  float: right;\n}\n.ts_continue_button {\n  margin-top: 12px;\n}\n.highlighted_cell {\n  color: #990000;\n  background-color: #ffffee;\n  text-align: center;\n}\n.light_grey {\n  color: #999999;\n}\nh2 {\n  font-size: 24px;  \n}\nh3 {\n  border-top-left-radius: 4px;\n  border-top-right-radius: 4px;\n  -webkit-border-top-left-radius: 4px;\n  -webkit-border-top-right-radius: 4px;\n  -moz-border-radius-topleft: 4px;\n  -moz-border-radius-topright: 4px;\n  font-size: 12px;\n  font-weight: normal;\n  margin-bottom: 0;\n  margin-right: 1px;\n  padding: 1px 0 0 5px;\n  width: 792px;\n  height: 22px;\n}\nh3 a:link, h3 a:visited {\n  background-color: white;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n  color: black;\n  float: right;\n  line-height: 17px;\n  margin-right: 3px;\n  margin-top: 2px;\n  padding: 0 8px;\n  text-decoration: none;\n}\nh3 a:hover {\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\n\n/* navigation */\nul#css_nav {\n  clear: both;\n  width:100%;\n  margin: 0px;\n  padding: 0px;\n  overflow: hidden;\n}\nul#css_nav li {\n  float: left;\n  background-color: #efefef;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n  list-style: none;\n  text-align:center;\n  margin: 0px 2px 20px 0px;\n  color: #666666;\n  font-size: 11px;\n  line-height: 20px;\n}\nul#css_nav li span {\n  font-size: 11px;\n  line-height: 20px;\n}\n\nul#css_nav li.css_nav_current_page {\n  background-color: #999999;\n  color: white;\n}\nul#css_nav li a:link, ul#css_nav li a:visited {\n  display: block;\n  text-decoration: none;\n  color: white;\n  background-color: #999999;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;  \n}\nul#css_nav li a:hover {\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\n.nav_1_pages li {\n  width: 100%;\n}\n.nav_2_pages li {\n  width: 49.7%;\n}\n.nav_3_pages li {\n  width: 33%;\n}\n.nav_4_pages li {\n  width: 24.7%;\n}\n.nav_5_pages li {\n  width: 19.7%;\n}\n.nav_6_pages li {\n  width: 16.4%;\n}\n.nav_7_pages li {\n  width: 14%;\n}\n.nav_8_pages li {\n  width: 12.2%;\n}\n\n/* notifications */\n.notify {\n  border: 1px solid #336699;\n  background-color: #ffffee;\n  color: #336699;\n  padding: 8px;\n  width: 400px;\n}\n.notify li { color: #336699; }\n.error {\n  font-size: 8pt;\n  border: 1px solid #cc0000;\n  background-color: #ffffee;\n  color: #cc0000;\n  padding: 8px;\n  width: 550px;\n}\n.error span {\n  color: #cc0000;\n  font-weight: bold;\n  margin-bottom: 4px;\n}\n\n/* forms */\ntable.table_1 > tbody > tr > td {\n  border-bottom: 1px solid #dddddd;\n}\n.table_1_bg td {\n  padding: 1px;\n  padding-left: 1px;\n  background-color: #336699;\n  border-bottom: 1px solid #cccccc;\n}\ntd.answer {\n  background-color: #efefef;\n}\n.pad_left {\n  padding-left: 4px;\n}\n.req {\n  color: #aa0000;  \n}\n.fb_error {\n  border: 1px solid #990000;\n  padding: 8px; \n  background-color: #ffefef;\n}\n\n/* for the code / markup editor */\n.editor {\n  background-color: white;\n  border: 1px solid #999999;\n  padding: 3px;\n}\n\n\n/* - - - \"Highlight Colour\" placeholder conditional CSS - - -*/\n{{if \$P.colours == \"Red\"}}\nh3 {\n  background-color: #cc3131;\n  color: white;\n}\nul#css_nav li a:hover {\n  background-color: #861e1e;\n}\nh3 a:hover {\n  background-color: #fac1c1;\n  color: black;\n}\n{{elseif \$P.colours == \"Orange\"}}\nh3 {\n  background-color: #ff9c00;\n  color: white;\n}\nul#css_nav li a:hover {\n  background-color: #4c3512;\n}\nh3 a:hover {\n  background-color: #ffefd5;\n  color: black;\n}\n{{elseif \$P.colours == \"Yellow\"}}\nh3 {\n  background-color: #FAEC0C;\n  color: #777777;\n}\nul#css_nav li a:hover {\n  background-color: #595900;\n}\nh3 a:hover {\n  background-color: #444000;\n  color: #ffffcc;\n}\n{{elseif \$P.colours == \"Green\"}}\nh3 {\n  background-color: #009211;\n  color: white;\n}\nul#css_nav li a:hover {\n  background-color: #004608;\n}\nh3 a:hover {\n  background-color: #daf4dd;\n  color: black;\n}\n{{elseif \$P.colours == \"Blue\"}}\nh3 {\n  background-color: #2969c9;\n  color: white;\n}\nh3 a:hover {\n  background-color: #a6c8f0;\n  color: black;\n}\nul#css_nav li a:hover {\n  background-color: #1e4580;\n}\n{{elseif \$P.colours == \"Grey\"}}\nh3 {\n  background-color: #777777;\n  color: white;\n}\nul#css_nav li a:hover {\n  background-color: #333333;\n}\nh3 a:hover {\n  background-color: #222222;\n  color: white;\n}\n{{else}}\nh3 {\n  background-color: #6D8AAC;\n  color: white;\n}\nul#css_nav li a:hover {\n  background-color: #2e425a;\n}\nh3 a:hover {\n  background-color: #c9e2ff;\n  color: black;\n}\n{{/if}}\n",
      "last_updated"  => "2012-01-31 23:07:47"
    )
  ),

  // placeholders
  "placeholders" => array(
    array(
      "placeholder_label" => "Highlight Colours",
      "placeholder"       => "colours",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Blue-Grey",
      "options" => array(
        array("option_text" => "Red"),
        array("option_text" => "Orange"),
        array("option_text" => "Yellow"),
        array("option_text" => "Green"),
        array("option_text" => "Blue"),
        array("option_text" => "Blue-Grey"),
        array("option_text" => "Grey")
      )
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "ProSimple",
  "version"     => "1.0",
  "description" => "A simple, professional-looking and attractive grey-themed Template Set without a header. It contains placeholders to let you choose the highlight colour, font and font size. Labels are placed directly above the fields to provide clear identification.",
  "is_complete" => "yes",
  "list_order"  => 2,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  {{\$R.styles}}\n  <script>\n  \$(function() {\n    \$(\"input,textarea,select\").bind(\"focus\", function() {\n      \$(this).closest(\"form\").find(\".ts_field\").removeClass(\"ts_field_row_selected\");\n      \$(this).closest(\".ts_field\").addClass(\"ts_field_row_selected\");\n    });\n    \$(\":text:visible:enabled:first\").focus();\n  });\n  </script>\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:800px\">\n    <div class=\"ts_content\">\n"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "  </div> <!-- ends class=\"ts_content\" div -->\n</div> <!-- ends class=\"ts_page\" div -->\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "{{navigation}}\n\n{{form_builder_edit_link}}\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\" \n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n  \n  <a name=\"s{{\$group.group_id}}\"></a>\n  {{if \$group.group_name}}\n    <h3>{{\$group.group_name|upper}}</h3>\n  {{else}}\n    <br />\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n\n    <ul class=\"ts_field\">\n      <li class=\"ts_field_label\">\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </li>\n      <li>\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n      </li>\n    </ul>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    <br />\n  {{/if}}\n{{/foreach}}\n\n{{continue_block}}\n    \n</form>"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "{{navigation}}\n\n{{form_builder_edit_link}}\n\n<h2>{{\$review_page_title}}</h2>\n\n<p>\n  Please review the information below to confirm it is correct. If you need to edit any\n  values, just click the EDIT link at the top right of the section.\n</p>\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n{{foreach from=\$grouped_fields item=curr_group}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  {{if \$fields|@count > 0}}\n  <h3><a href=\"?page={{\$group.custom_data|default:1}}#s{{\$group.group_id}}\">EDIT</a>{{\$group.group_name|upper}}</h3>\n \n    <table class=\"ts_review_table\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td valign=\"top\" width=\"200\">{{\$curr_field.field_title}}</td>\n      <td valign=\"top\">\n        {{display_custom_field form_id=\$form_id view_id=\$view_id submission_id=\$submission_id\n          value=\$curr_field.submission_value field_info=\$curr_field field_types=\$field_types\n          settings=\$settings}}\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>\n    \n    <br />\n  {{/if}}\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "{{navigation}}\n\n{{form_builder_edit_link}}\n\n{{\$thankyou_page_content}}\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "{{\$form_offline_page_content}}"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Simple",
      "content"       => "<div>\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Detailed",
      "content"       => "<div class=\"ts_continue_block\">\n  <input type=\"submit\" value=\"Continue\" name=\"form_tools_continue\">  \n  This is page <b>{{\$current_page}}</b> of <b>{{\$num_pages}}</b>. You must complete all steps in order for your submission to be processed. Please click continue.\n</div>\n"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - 1",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li><a href=\"?page={{\$i}}\">{{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$page_info.page_name}}</li>\n    {{else}}\n      <li>{{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - 2",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li><a href=\"{{\$filename}}?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$i}}. {{\$page_info.page_name}}</li>\n    {{else}}\n      <li>{{\$i}}. {{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - 3",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li><a href=\"{{\$filename}}?page={{\$i}}\">&raquo; {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">&raquo; {{\$page_info.page_name}}</li>\n    {{else}}\n      <li>&raquo; {{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - None",
      "content"       => ""
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"fb_error\">{{\$validation_error}}</div>\n{{/if}}\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "General Styles",
      "placeholder"   => "styles",
      "content"       => "{{if \$P.colours == \"Blue\"}}\n  {{assign var=header_bg value=\"#388ef4\"}}\n  {{assign var=border_colour value=\"#C4DFFF\"}}\n  {{assign var=selected_row_bg value=\"#d5e8ff\"}}\n  {{assign var=selected_row_bottom value=\"#d5e8ff\"}}\n  {{assign var=content_border value=\"#94c5fe\"}}\n  {{assign var=continue_block_colour value=\"#f1f7ff\"}}\n{{elseif \$P.colours == \"Green\"}}\n  {{assign var=header_bg value=\"#0b9c00\"}}\n  {{assign var=border_colour value=\"#e7ffe5\"}}\n  {{assign var=selected_row_bg value=\"#d9f4cb\"}}\n  {{assign var=selected_row_bottom value=\"#d9f4cb\"}}\n  {{assign var=content_border value=\"#ade0aa\"}}\n  {{assign var=continue_block_colour value=\"#E9F9E7\"}}\n{{elseif \$P.colours == \"Purple\"}}\n  {{assign var=header_bg value=\"#ac52ce\"}}\n  {{assign var=border_colour value=\"#f7e0ff\"}}\n  {{assign var=selected_row_bg value=\"#f6dfff\"}}\n  {{assign var=selected_row_bottom value=\"#f6dfff\"}}\n  {{assign var=content_border value=\"#e9c1f8\"}}\n  {{assign var=continue_block_colour value=\"#ffffcc\"}}\n{{elseif \$P.colours == \"Orange\"}}\n  {{assign var=header_bg value=\"#ffa904\"}}\n  {{assign var=border_colour value=\"#ffa904\"}}\n  {{assign var=selected_row_bg value=\"#ffd789\"}}\n  {{assign var=selected_row_bottom value=\"#ffa904\"}}\n  {{assign var=content_border value=\"#CE911A\"}}\n  {{assign var=continue_block_colour value=\"#f1f7ff\"}}\n{{/if}}\n\nbody {\n  text-align: center;\n  padding: 0px;\n  margin: 0px;\n  background-color: #999999;\n}\ntd, th, p, input, textarea, select,ul,li,div, span {\n  font-family: {{\$P.font}};\n  font-size: {{\$P.font_size}};\n  color: #555555;\n}\ntd, th, p, textarea, ul,li, div {\n  line-height: 22px;\n}\na:link, a:visited {\n  color: #336699;\n}\ntable {\n  empty-cells: show;\n}\n#form_builder__edit_link {\n  float: right; \n}\n.req {\n  color: #aa0000;\n}\n.fb_error {\n  margin-top: 16px;\n  padding: 8px;\n  box-shadow: 1px 2px 2px #878787;\n  background-color: #ffefef;\n}\n\n\n/* page sections */\n.ts_page:after {\n  -moz-transform: translate(0pt, 0pt);\n  background: none repeat scroll 0 0 transparent;\n  border-radius: 20px 20px 20px 20px;\n  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);\n  content: \"\";\n  left: 0;\n  position: absolute;\n  width: 100%;\n  z-index: -2;\n}\n.ts_page {\n  margin: 20px auto 0px;\n  background: #ccc;\n  position:relative;\n  box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);\n  -moz-box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);\n  -webkit-box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);\n  text-align: left;\n  border: 5px solid {{\$border_colour}};\n}\n.ts_header {\n  background: none repeat scroll 0 0 rgba(0, 0, 0, 0.5);\n  border: 3px solid #CCCCCC;\n  height: 140px;\n  background: #3a3a3a; /* Old browsers */\n  background: -moz-linear-gradient(45deg,  #777777 0%, #999999 100%); /* FF3.6+ */\n  background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#3a3a3a), color-stop(100%,#4f4f4f)); /* Chrome,Safari4+ */\n  background: -webkit-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* Chrome10+,Safari5.1+ */\n  background: -o-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* Opera 11.10+ */\n  background: -ms-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* IE10+ */\n  background: linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* W3C */\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3a3a3a', endColorstr='#4f4f4f',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */\n  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);\n}\n.ts_header h1 {\n  margin: 56px 50px;\n  padding: 0px;\n  font-size: 20px;\n  color: white;\n}\n.ts_content {\n  background-color: white;\n  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);\n  padding: 25px 50px;\n  border: 1px solid {{\$content_border}};\n}\n.ts_continue_block {\n  margin-top: 16px;\n  padding: 8px;\n  box-shadow: 1px 2px 2px #878787;\n  background-color: {{\$continue_block_colour}};\n}\n.ts_continue_block input {\n  float: right;\n}\n.ts_field_row_selected {\n  background-color: {{\$selected_row_bg}};\n  border-bottom: 1px solid {{\$selected_row_bottom}};\n}\n.ts_continue_button {\n  margin-top: 12px;\n}\n.highlighted_cell {\n  color: #990000;\n  background-color: #ffffee;\n  text-align: center;\n}\n.light_grey {\n  color: #999999;\n}\n.ts_field {\n  border-bottom: 1px solid #efefef;\n  padding: 10px 6px 15px;\n  list-style: none;\n  margin: 0px;\n}\n.ts_review_table td {\n  border-bottom: 1px solid #efefef;\n  padding: 3px 5px 2px;\n}\nh2 {\n  font-size: 24px;\n}\nh3 {\n  background-color: {{\$header_bg}};\n  color: white;\n  font-size: 12px;\n  font-weight: normal;\n  margin-bottom: 0;\n  padding: 1px 0 0 5px;\n  height: 22px;\n}\nh3 a:link, h3 a:visited {\n  background-color: white;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n  color: black;\n  float: right;\n  line-height: 17px;\n  margin-right: 3px;\n  margin-top: 2px;\n  padding: 0 8px;\n  text-decoration: none;\n}\nh3 a:hover {\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\n\n/* navigation */\nul#css_nav {\n  clear: both;\n  width:100%;\n  margin: 0px;\n  padding: 0px;\n  overflow: hidden;\n}\nul#css_nav li {\n  float: left;\n  background-color: #efefef;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n  list-style: none;\n  text-align:center;\n  margin: 0px 2px 20px 0px;\n  color: #666666;\n  font-size: 11px;\n  line-height: 20px;\n}\nul#css_nav li.css_nav_current_page {\n  background-color: #999999;\n  color: white;\n}\nul#css_nav li a:link, ul#css_nav li a:visited {\n  display: block;\n  text-decoration: none;\n  color: white;\n  background-color: #999999;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\nul#css_nav li a:hover {\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\n.nav_1_pages li {\n  width: 100%;\n}\n.nav_2_pages li {\n  width: 50%;\n}\n.nav_3_pages li {\n  width: 33%;\n}\n.nav_4_pages li {\n  width: 24.7%;\n}\n.nav_5_pages li {\n  width: 19.5%;\n}\n.nav_6_pages li {\n  width: 16%;\n}\n.nav_7_pages li {\n  width: 13%;\n}\n.nav_8_pages li {\n  width: 12%;\n}\n\n/* notifications */\n.notify {\n  border: 1px solid #336699;\n  background-color: #ffffee;\n  color: #336699;\n  padding: 8px;\n  width: 400px;\n}\n.notify li { color: #336699; }\n.error {\n  font-size: 8pt;\n  border: 1px solid #cc0000;\n  background-color: #ffffee;\n  color: #cc0000;\n  padding: 8px;\n  width: 550px;\n}\n.error span {\n  color: #cc0000;\n  font-weight: bold;\n  margin-bottom: 4px;\n}\n\n/* for the code / markup editor */\n.editor {\n  background-color: white;\n  border: 1px solid #999999;\n  padding: 3px;\n}\nul#css_nav li a:hover {\n  background-color: #2e425a;\n}\nh3 a:hover {\n  background-color: #c9e2ff;\n  color: black;\n}\n\n",
      "last_updated"  => "2012-02-02 16:22:35"
    )
  ),

  // placeholders
  "placeholders" => array(
    array(
      "placeholder_label" => "Colour Palette",
      "placeholder"       => "colours",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Green",
      "options" => array(
        array("option_text" => "Blue"),
        array("option_text" => "Green"),
        array("option_text" => "Orange"),
        array("option_text" => "Purple")
      )
    ),
    array(
      "placeholder_label" => "Font",
      "placeholder"       => "font",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Verdana",
      "options" => array(
        array("option_text" => "Arial"),
        array("option_text" => "Georgia, Verdana"),
        array("option_text" => "\"Lucida Grande\",\"Lucida Sans Unicode\", Tahoma, sans-serif"),
        array("option_text" => "Tahoma"),
        array("option_text" => "Verdana")
      )
    ),
    array(
      "placeholder_label" => "Font Size",
      "placeholder"       => "font_size",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "9pt",
      "options" => array(
        array("option_text" => "8pt"),
        array("option_text" => "9pt"),
        array("option_text" => "10pt"),
        array("option_text" => "11pt"),
        array("option_text" => "12pt")
      )
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "Conformist",
  "version"     => "1.0",
  "description" => "A clean blue Template Set with delicate CSS3 gradients and tab-like, top-row page navigation.",
  "is_complete" => "yes",
  "list_order"  => 3,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  <link href=\"http://fonts.googleapis.com/css?family={{\$P.font|regex_replace:'/[ ]/':'+'}}\" rel='stylesheet' type='text/css'>  \n  {{\$required_resources}}\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:900px\">\n    <div class=\"ts_header\">\n      {{form_builder_edit_link}}\n      <h1>{{\$form_name}}</h1>\n    </div>\n\n"
    ),
    array(
      "template_type" => "header",
      "template_name" => "No Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:900px\">\n\n"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "  <div class=\"ts_footer\">{{\$P.footer_html}}</div>\n</div> <!-- ends class=\"ts_page\" div -->\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_content\">\n  <div class=\"ts_content_inner\">\n\n  <h2>{{\$page_name}}</h2>\n\n  {{error_message}}\n\n  <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n    id=\"ts_form_element_id\" name=\"edit_submission_form\">\n  {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n    {{assign var=group value=\$curr_group.group}}\n    {{assign var=fields value=\$curr_group.fields}}\n\n      <a name=\"s{{\$group.group_id}}\"></a>\n    {{if \$group.group_name}}\n      <h3>{{\$group.group_name}}</h3>\n    {{else}}\n      <br />\n    {{/if}}\n\n    {{if \$fields|@count > 0}}\n    <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n    {{/if}}\n    \n    {{foreach from=\$fields item=curr_field}}\n      {{assign var=field_id value=\$field.field_id}}\n      <tr>\n        <td width=\"180\" valign=\"top\">\n          {{\$curr_field.field_title}}\n          <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n        </td>\n        <td class=\"answer\" valign=\"top\">\n          <div class=\"pad_left\">\n          {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n            settings=\$settings submission_id=\$submission_id}}\n          </div>\n        </td>\n      </tr>\n    {{/foreach}}\n\n    {{if \$fields|@count > 0}}\n      </table>  \n    {{/if}}\n\n  {{/foreach}}\n\n  {{continue_block}}\n\n  </form>\n    \n  </div>\n</div>\n"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_content\">\n  <div class=\"ts_content_inner\">\n\n  <h2>{{\$review_page_title}}</h2>\n\n  <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n  {{foreach from=\$grouped_fields item=curr_group}}\n    {{assign var=group value=\$curr_group.group}}\n    {{assign var=fields value=\$curr_group.fields}}\n\n    {{if \$fields|@count > 0}}\n      <h3>\n        <a href=\"?page={{\$group.custom_data}}#s{{\$group.group_id}}\">EDIT</a>\n        {{\$group.group_name}}\n      </h3>\n  \n      <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n    {{/if}}\n\n    {{foreach from=\$fields item=curr_field}}\n      {{assign var=field_id value=\$field.field_id}}\n      <tr>\n        <td width=\"200\" class=\"pad_left_small\" valign=\"top\">{{\$curr_field.field_title}}</td>\n        <td class=\"answer\" valign=\"top\">\n          <div class=\"pad_left\">\n          {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n            field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n          </div>\n        </td>\n      </tr>\n    {{/foreach}}\n\n    {{if \$fields|@count > 0}}\n      </table>    \n    {{/if}}\n  {{/foreach}}\n\n  {{continue_block}}\n\n  </form>\n\n  </div>\n</div>\n\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_content\">\n  <div class=\"ts_content_inner\">\n    {{\$thankyou_page_content}}\n  </div>\n</div>\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "{{\$form_offline_page_content}}"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Button Only",
      "content"       => "<div class=\"ts_continue_button\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Detailed",
      "content"       => "<div class=\"ts_continue_block\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n  This is page <b>{{\$current_page}}</b> of <b>{{\$num_pages}}</b>. You must complete \n  all steps in order for your submission to be processed. Please click continue.\n</div>\n\n"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Arrows",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{assign var=a value=\" &raquo;\"}}\n    {{if \$smarty.foreach.row.last}}\n      {{assign var=a value=\"\"}}\n    {{/if}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li class=\"completed_page\"><a href=\"{{\$filename}}?page={{\$i}}\">{{\$page_info.page_name}}{{\$a}}</a></li>\n    {{elseif \$i != \$current_page && \$current_page == \$num_pages}}\n      <li class=\"completed_page\"><span>{{\$page_info.page_name}}{{\$a}}</span></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$page_info.page_name}}{{\$a}}</li>\n    {{else}}\n      <li>{{\$page_info.page_name}}{{\$a}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Numbered",
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n      <li class=\"completed_page\"><a href=\"{{\$filename}}?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n    {{elseif \$i != \$current_page && \$current_page == \$num_pages}}\n      <li class=\"completed_page\"><span>{{\$i}}. {{\$page_info.page_name}}</span></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n      <li class=\"css_nav_current_page\">{{\$i}}. {{\$page_info.page_name}}</li>\n    {{else}}\n      <li>{{\$i}}. {{\$page_info.page_name}}</li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "No Navigation",
      "content"       => "<ul id=\"css_nav\">\n  <li><span></span></li>\n</ul>"
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"fb_error\">{{\$validation_error}}</div>\n{{/if}}\n\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "General Styles",
      "placeholder"   => "styles",
      "content"       => "html {\n  height: 100%; \n}\nbody {\n  height: 100%;\n  text-align: center;\n  padding: 0px;\n  margin: 0px;\n  background: rgb(106,147,184);\n  background: -moz-linear-gradient(top,  rgba(106,147,184,1) 0%, rgba(115,151,183,1) 100%);\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(106,147,184,1)), color-stop(100%,rgba(115,151,183,1)));\n  background: -webkit-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);\n  background: -o-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);\n  background: -ms-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);\n  background: linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6a93b8', endColorstr='#7397b7',GradientType=0 );\n  background-repeat: no-repeat;\n  background-attachment: fixed;\n}\ntd, th, p, input, textarea, select,ul,li,div, span {\n  font-family: \"Lucida Grande\",\"Lucida Sans Unicode\", Tahoma, sans-serif;\n  font-size: 12px;\n  color: #555555;\n}\ntd, th, p, textarea, ul, li, div {\n  line-height: 22px;\n}\na:link, a:visited {\n  color: #336699;\n}\ntable {\n  empty-cells: show;\n}\n\n/* page sections */\n.ts_page:after {\n  -moz-transform: translate(0pt, 0pt);\n  background: none repeat scroll 0 0 transparent;\n  border-radius: 20px 20px 20px 20px;\n  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);\n  content: \"\";\n  left: 0;\n  position: absolute;\n  width: 100%;\n  z-index: -2;\n}\n.ts_page {\n  margin: 40px auto;\n  position: relative;\n  text-align: left;\n}\n.ts_header h1 {\n  margin: 0px 0px 42px 20px;\n  padding: 0px;\n  font-size: {{\$P.font_size}};\n  color: white;\n  font-family: \"{{\$P.font}}\", \"Lucida Grande\", Arial;\n  font-weight: normal;\n}\n.ts_footer {\n  background: rgb(64,86,107);\n  background: -moz-linear-gradient(top,  rgb(64,86,107) 0%, rgb(44,61,76) 100%);\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(64,86,107)), color-stop(100%,rgb(44,61,76)));\n  background: -webkit-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: -o-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: -ms-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#40566b', endColorstr='#2c3d4c',GradientType=0 );\n  -webkit-border-bottom-left-radius: 6px;\n  -webkit-border-bottom-right-radius: 6px;\n  -moz-border-radius-bottomleft: 6px;\n  -moz-border-radius-bottomright: 6px;\n  border-bottom-left-radius: 6px;\n  border-bottom-right-radius: 6px;\n  padding: 10px 0px;\n  text-align: center;\n  color: #dddddd;\n  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);\n  height: 5px;\n}\n.ts_content {\n  background-color: white;\n  border: 1px solid #777777;\n  border-top: 0px;\n  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);\n  padding: 25px 50px;\n}\n.ts_continue_block {\n  margin-top: 16px;\n  background-color: #ffffdd;\n  padding: 8px;\n  box-shadow: 1px 2px 2px #878787;\n}\n.ts_continue_block input {\n  float: right;\n}\n.ts_continue_button {\n  margin-top: 12px;\n}\n.light_grey {\n  color: #999999;\n}\nh2 {\n  font-size: 20px;  \n}\n.ts_heading {\n  font-size: 20px;  \n}\n\nh3 {\n  border-top-left-radius: 4px;\n  border-top-right-radius: 4px;\n  -webkit-border-top-left-radius: 4px;\n  -webkit-border-top-right-radius: 4px;\n  -moz-border-radius-topleft: 4px;\n  -moz-border-radius-topright: 4px;\n  font-size: 12px;\n  font-weight: normal;\n  margin-bottom: 0;\n  margin-right: 1px;\n  padding: 1px 0 0 5px;\n  width: 792px;\n  background-color: #36485a;\n  color: white;\n  height: 22px;\n}\nh3 a:link, h3 a:visited {\n  background-color: white;\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n  color: black;\n  float: right;\n  line-height: 17px;\n  margin-right: 3px;\n  margin-top: 2px;\n  padding: 0 8px;\n  text-decoration: none;\n}\nh3 a:hover {\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\n\n/* navigation */\nul#css_nav {\n  clear: both;\n  margin: 0px;\n  padding: 0px 40px;\n  overflow: hidden;\n  background: rgb(64,86,107);\n  background: -moz-linear-gradient(top,  rgb(64,86,107) 0%, rgb(44,61,76) 100%);\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(64,86,107)), color-stop(100%,rgb(44,61,76)));\n  background: -webkit-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: -o-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: -ms-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  background: linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#40566b', endColorstr='#2c3d4c',GradientType=0 );\n  -webkit-border-top-left-radius: 6px;\n  -webkit-border-top-right-radius: 6px;\n  -moz-border-radius-topleft: 6px;\n  -moz-border-radius-topright: 6px;\n  border-top-left-radius: 6px;\n  border-top-right-radius: 6px;\n  height: 38px;\n}\nul#css_nav li {\n  float: left;\n  list-style: none;\n  text-align:center;\n  color: #dddddd;\n  font-size: 11px;\n  padding: 8px 0px;\n}\nul#css_nav li span {\n  font-size: 11px;\n}\n\nul#css_nav li.completed_page {\n  padding: 0px;\n}\nul#css_nav li.css_nav_current_page {\n  background: rgb(249,249,249);\n  background: -moz-linear-gradient(top, rgba(249,249,249,1) 0%, rgba(255,255,255,1) 100%);\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(249,249,249,1)), color-stop(100%,rgba(255,255,255,1)));\n  background: -webkit-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);\n  background: -o-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);\n  background: -ms-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);\n  background: linear-gradient(top,  rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#ffffff',GradientType=0 );\n  color: #000000;\n}\nul#css_nav li a:link, ul#css_nav li a:visited, ul#css_nav li span {\n  display: block;\n  text-decoration: none;\n  color: white;\n  background-color: #333333;\n  padding: 8px 0px;\n  opacity: 0.5;\n  filter: alpha(opacity=50);  \n}\n ul#css_nav li a:hover {\n  background-color: #222222;\n  opacity: 0.9;\n  filter: alpha(opacity=90);\n}\n\n.nav_1_pages li {\n  width: 150px;\n}\n.nav_2_pages li {\n  width: 150px;\n}\n.nav_3_pages li {\n  width: 150px;\n}\n.nav_4_pages li {\n  width: 150px;\n}\n.nav_5_pages li {\n  width: 150px;\n}\n.nav_6_pages li {\n  width: 136px;\n}\n.nav_7_pages li {\n  width: 116px;\n}\n.nav_8_pages li {\n  width: 102px;\n}\n\n\n/* notifications */\n.notify {\n  border: 1px solid #336699;\n  background-color: #ffffee;\n  color: #336699;\n  padding: 8px;\n  width: 400px;\n}\n.notify li { color: #336699; }\n.error {\n  font-size: 8pt;\n  border: 1px solid #cc0000;\n  background-color: #ffffee;\n  color: #cc0000;\n  padding: 8px;\n  width: 550px;\n}\n.error span {\n  color: #cc0000;\n  font-weight: bold;\n  margin-bottom: 4px;\n}\n\n/* forms */\ntable.table_1 > tbody > tr > td {\n  border-bottom: 1px solid #dddddd;\n}\n.table_1_bg td {\n  padding: 1px;\n  padding-left: 1px;\n  background-color: #336699;\n  border-bottom: 1px solid #cccccc;\n}\ntd.answer {\n  background-color: #efefef;\n}\n.pad_left {\n  padding-left: 4px;\n}\n.req {\n  color: #aa0000;  \n}\n.fb_error {\n  border: 1px solid #990000;\n  padding: 8px; \n  background-color: #ffefef;\n}\n\n/* for the code / markup editor */\n.editor {\n  background-color: white;\n  border: 1px solid #999999;\n  padding: 3px;\n}\n#form_builder__edit_link {\n  position: absolute;\n  right: 5px;\n  top: 0px;\n  text-decoration: none;\n}\n#form_builder__edit_link:hover {\n  color: #990000;\n  text-decoration: underline;\n}\n\n",
      "last_updated"  => "2012-02-03 17:42:30"
    )
  ),

  // placeholders
  "placeholders" => array(
    array(
      "placeholder_label" => "Title Font",
      "placeholder"       => "font",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Italianno",
      "options" => array(
        array("option_text" => "Aladin"),
        array("option_text" => "Alegreya SC"),
        array("option_text" => "Alike Angular"),
        array("option_text" => "Almendra SC"),
        array("option_text" => "Chango"),
        array("option_text" => "Fredericka the Great"),
        array("option_text" => "Frijole"),
        array("option_text" => "Gudea"),
        array("option_text" => "Italianno"),
        array("option_text" => "Jim Nightshade"),
        array("option_text" => "Lustria"),
        array("option_text" => "Miss Fajardose"),
        array("option_text" => "Montez"),
        array("option_text" => "Telex"),
        array("option_text" => "Yesteryear")
      )
    ),
    array(
      "placeholder_label" => "Title Font Size",
      "placeholder"       => "font_size",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "44px",
      "options" => array(
        array("option_text" => "20px"),
        array("option_text" => "22px"),
        array("option_text" => "24px"),
        array("option_text" => "26px"),
        array("option_text" => "28px"),
        array("option_text" => "30px"),
        array("option_text" => "32px"),
        array("option_text" => "34px"),
        array("option_text" => "36px"),
        array("option_text" => "38px"),
        array("option_text" => "40px"),
        array("option_text" => "42px"),
        array("option_text" => "44px"),
        array("option_text" => "46px"),
        array("option_text" => "48px"),
        array("option_text" => "50px"),
        array("option_text" => "52px"),
        array("option_text" => "54px"),
        array("option_text" => "56px"),
        array("option_text" => "58px"),
        array("option_text" => "60px")
      )
    ),
    array(
      "placeholder_label" => "Footer HTML",
      "placeholder"       => "footer_html",
      "field_type"        => "textarea",
      "field_orientation" => "horizontal",
      "default_value"     => "yoursite.com",
      "options" => array()
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "Illuminate",
  "version"     => "1.0",
  "description" => "A bold, bright-coloured theme with choice of colour set and footer content. Navigation is required and displayed as a left column. Uses the \"Trykker\" Google Web Font for a little extra snap!",
  "is_complete" => "yes",
  "list_order"  => 4,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  <link href=\"http://fonts.googleapis.com/css?family=Trykker\" rel=\"stylesheet\" type=\"text/css\">\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_head_bg\"></div>\n  <div class=\"ts_page\" style=\"width:960px\">\n    <div class=\"ts_header\"><h1>{{\$form_name}}</h1></div>\n    {{form_builder_edit_link}}\n\n    <div class=\"ts_content\">\n      \n\n"
    ),
    array(
      "template_type" => "header",
      "template_name" => "No Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  {{\$required_resources}}\n  <link href=\"http://fonts.googleapis.com/css?family=Trykker\" rel=\"stylesheet\" type=\"text/css\">\n  {{\$R.styles}}\n</head>\n<body>\n  <div class=\"ts_page\" style=\"width:960px\">\n    {{form_builder_edit_link}}\n    <div class=\"ts_content\">"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "<div class=\"clear\"></div>\n\n  </div> <!-- ends class=\"ts_content\" div -->\n</div> <!-- ends class=\"ts_page\" div -->\n\n<div id=\"ts_footer\">{{\$P.footer_html}}</div>\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_page_content\">\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  <a name=\"s{{\$group.group_id}}\"></a>\n  <fieldset>\n  {{if \$group.group_name}}\n    <legend>{{\$group.group_name}}</legend>\n  {{/if}}\n\n  {{if \$fields|@count > 0}}\n  <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"688\">\n  {{/if}}\n    \n  {{foreach from=\$fields item=curr_field name=i}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"180\" valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </td>\n      <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>  \n  {{/if}}\n\n  </fieldset>\n\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n  \n</div>\n"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_page_content\">\n\n<h2>{{\$review_page_title}}</h2>\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n{{foreach from=\$grouped_fields item=curr_group}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  <fieldset>\n  {{if \$fields|@count > 0}}\n    <legend>{{\$group.group_name}} <span class=\"edit_link\">(<a \n      href=\"?page={{\$group.custom_data}}#s{{\$group.group_id}}\">EDIT</a>)</span>\n    </legend>\n\n    <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"668\">\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field name=i}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"200\" valign=\"top\" \n        {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>{{\$curr_field.field_title}}</td>\n      <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n        {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n          field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>    \n  {{/if}}\n  </fieldset>\n\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n  \n</div>\n\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "{{navigation}}\n\n<div class=\"ts_page_content\">\n{{\$thankyou_page_content}}  \n</div>\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "{{\$form_offline_page_content}}"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Button Only",
      "content"       => "<div class=\"ts_continue_button\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation",
      "content"       => "{{if \$current_page == \$num_pages}}\n\n<ul id=\"ts_css_nav\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$i != \$num_pages}}\n  <li class=\"completed_page\"><div>&raquo; {{\$page_info.page_name}}</div></li>\n    {{else}}\n  <li class=\"css_nav_current_page\"><div>&raquo; {{\$page_info.page_name}}</div></li>\n    {{/if}}\n  {{/foreach}}\n</ul>\n\n{{else}}\n\n<ul id=\"ts_css_nav\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n  <li class=\"completed_page\"><a href=\"?page={{\$i}}\">&raquo; {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n  <li class=\"css_nav_current_page\"><div>&raquo; {{\$page_info.page_name}}</div></li>\n    {{else}}\n  <li><div>&raquo; {{\$page_info.page_name}}</div></li>\n    {{/if}}\n  {{/foreach}}\n</ul>\n\n{{/if}}\n\n"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Numbered",
      "content"       => "<ul id=\"ts_css_nav\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n  <li class=\"completed_page\"><a href=\"?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i || \$current_page == \$num_pages}}\n  <li class=\"css_nav_current_page\"><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n    {{else}}\n  <li><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"fb_error\">{{\$validation_error}}</div>\n{{/if}}\n\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "General Styles",
      "placeholder"   => "styles",
      "content"       => "{{* This top section defines the colours, based on the selected Colour placeholder *}}\n{{assign var=header_colour value=\"#ffffff\"}}\n{{assign var=header_shadow value=\"on\"}}\n{{if \$P.colours == \"Red\"}}\n  {{assign var=c1 value=\"#950000\"}}\n  {{assign var=c2 value=\"#af0a0a\"}}\n  {{assign var=link_colour value=\"#ffffcc\"}}\n  {{assign var=line value=\"#670000\"}}\n  {{assign var=fieldset_bg value=\"#780404\"}}\n  {{assign var=fieldset_lines value=\"#660909\"}}\n  {{assign var=fieldset_colour value=\"#FFFF99\"}}\n  {{assign var=fieldset_shadow value=\"on\"}}\n  {{assign var=font_colour value=\"#ffffff\"}}\n  {{assign var=page_heading value=\"#000000\"}}\n  {{assign var=nav_next_page value=\"#999999\"}}\n  {{assign var=nav_prev_page_text value=\"#999999\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#D78B00\"}}\n  {{assign var=nav_prev_page_border value=\"#999999\"}}\n  {{assign var=submit_btn_over value=\"#ff3c00\"}}\n  {{assign var=edit_link_colour value=\"#ffffff\"}}\n{{elseif \$P.colours == \"Orange\"}}\n  {{assign var=c1 value=\"#ffa500\"}}\n  {{assign var=c2 value=\"#ffb12b\"}}\n  {{assign var=link_colour value=\"#990000\"}}\n  {{assign var=line value=\"#ffc558\"}}\n  {{assign var=fieldset_bg value=\"#ef9c00\"}}\n  {{assign var=fieldset_lines value=\"#d28900\"}}\n  {{assign var=fieldset_colour value=\"#FFFF99\"}}\n  {{assign var=fieldset_shadow value=\"on\"}}\n  {{assign var=font_colour value=\"#333333\"}}\n  {{assign var=page_heading value=\"#ffffff\"}}\n  {{assign var=nav_next_page value=\"#555555\"}}\n  {{assign var=nav_prev_page_text value=\"#AF8D4F\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#D78B00\"}}\n  {{assign var=nav_prev_page_border value=\"#E69500\"}}\n  {{assign var=submit_btn_over value=\"#ff3c00\"}}\n  {{assign var=edit_link value=\"#990000\"}}\n{{elseif \$P.colours == \"Green\"}}\n  {{assign var=c1 value=\"#299a0b\"}}\n  {{assign var=c2 value=\"#31a612\"}}\n  {{assign var=link_colour value=\"#FFFF99\"}}\n  {{assign var=line value=\"#1c7e00\"}}\n  {{assign var=fieldset_bg value=\"#228a00\"}}\n  {{assign var=fieldset_lines value=\"#1e7d00\"}}\n  {{assign var=fieldset_colour value=\"#FFFF99\"}}\n  {{assign var=fieldset_shadow value=\"on\"}}\n  {{assign var=font_colour value=\"#eeeeee\"}}\n  {{assign var=page_heading value=\"#333333\"}}\n  {{assign var=nav_next_page value=\"#0f4f00\"}}\n  {{assign var=nav_prev_page_text value=\"#136600\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#135205\"}}\n  {{assign var=nav_prev_page_border value=\"#1e710b\"}}\n  {{assign var=submit_btn_over value=\"#0093E8\"}}\n  {{assign var=edit_link value=\"#990000\"}}\n{{elseif \$P.colours == \"Blue\"}}\n  {{assign var=c1 value=\"#0083cf\"}}\n  {{assign var=c2 value=\"#0690e0\"}}\n  {{assign var=link_colour value=\"#FFFF99\"}}\n  {{assign var=line value=\"#0c5e8d\"}}\n  {{assign var=fieldset_bg value=\"#0878b8\"}}\n  {{assign var=fieldset_lines value=\"#0669a2\"}}\n  {{assign var=fieldset_colour value=\"#FFFF99\"}}\n  {{assign var=fieldset_shadow value=\"on\"}}\n  {{assign var=font_colour value=\"#eeeeee\"}}\n  {{assign var=page_heading value=\"#222222\"}}\n  {{assign var=nav_next_page value=\"#333333\"}}\n  {{assign var=nav_prev_page_text value=\"#efefef\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#3396e2\"}}\n  {{assign var=nav_prev_page_border value=\"#cccccc\"}}\n  {{assign var=submit_btn_over value=\"#621111\"}}\n  {{assign var=edit_link value=\"#621111\"}}\n{{elseif \$P.colours == \"Black\"}}\n  {{assign var=c1 value=\"#222222\"}}\n  {{assign var=c2 value=\"#333333\"}}\n  {{assign var=link_colour value=\"#c8ebff\"}}\n  {{assign var=line value=\"#444444\"}}\n  {{assign var=fieldset_bg value=\"#353535\"}}\n  {{assign var=fieldset_lines value=\"#444444\"}}\n  {{assign var=fieldset_colour value=\"#c8ebff\"}}\n  {{assign var=fieldset_shadow value=\"on\"}}\n  {{assign var=font_colour value=\"#efefef\"}}\n  {{assign var=page_heading value=\"#eeeeee\"}}\n  {{assign var=nav_next_page value=\"#999999\"}}\n  {{assign var=nav_prev_page_text value=\"#3a8ab8\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#3a8ab8\"}}\n  {{assign var=nav_prev_page_border value=\"#4a99c7\"}}\n  {{assign var=submit_btn_over value=\"#3a8ab8\"}}\n  {{assign var=edit_link value=\"#c8ebff\"}}\n{{elseif \$P.colours == \"Grey\"}}\n  {{assign var=c1 value=\"#dddddd\"}}\n  {{assign var=c2 value=\"#ffffff\"}}\n  {{assign var=link_colour value=\"#0033cc\"}}\n  {{assign var=line value=\"#cccccc\"}}\n  {{assign var=fieldset_bg value=\"#f2f2f2\"}}\n  {{assign var=fieldset_lines value=\"#aaaaaa\"}}\n  {{assign var=fieldset_colour value=\"#888888\"}}\n  {{assign var=fieldset_shadow value=\"off\"}}\n  {{assign var=font_colour value=\"#333333\"}}\n  {{assign var=page_heading value=\"#555555\"}}\n  {{assign var=nav_next_page value=\"#999999\"}}\n  {{assign var=nav_prev_page_text value=\"#888888\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#888888\"}}\n  {{assign var=nav_prev_page_border value=\"#888888\"}}\n  {{assign var=submit_btn_over value=\"#3a8ab8\"}}\n  {{assign var=edit_link value=\"#c8ebff\"}}\n{{elseif \$P.colours == \"White\"}}\n  {{assign var=c1 value=\"#ffffff\"}}\n  {{assign var=c2 value=\"#ffffff\"}}\n  {{assign var=header_colour value=\"#222222\"}}\n  {{assign var=header_shadow value=\"off\"}}\n  {{assign var=link_colour value=\"#0093e8\"}}\n  {{assign var=line value=\"#000000\"}}\n  {{assign var=fieldset_bg value=\"#ffffff\"}}\n  {{assign var=fieldset_lines value=\"#000000\"}}\n  {{assign var=fieldset_colour value=\"#000000\"}}\n  {{assign var=fieldset_shadow value=\"off\"}}\n  {{assign var=font_colour value=\"#333333\"}}\n  {{assign var=page_heading value=\"#555555\"}}\n  {{assign var=nav_next_page value=\"#999999\"}}\n  {{assign var=nav_prev_page_text value=\"#888888\"}}\n  {{assign var=nav_prev_page_bg_over value=\"#888888\"}}\n  {{assign var=nav_prev_page_border value=\"#888888\"}}\n  {{assign var=submit_btn_over value=\"#0093e8\"}}\n  {{assign var=edit_link value=\"#0093e8\"}}\n{{/if}}\nhtml {\n  height: 100%;\n  margin: 0px;\n}\nbody {\n  height: 100%;\n  text-align: center;\n  padding: 0px;\n  margin: 0px;\n  background: {{\$c2}}; /* Old browsers */\n  background: -moz-linear-gradient(top, {{\$c1}} 0%, {{\$c2}} 100%); /* FF3.6+ */\n  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,{{\$c1}}), color-stop(100%,{{\$c2}})); /* Chrome,Safari4+ */\n  background: -webkit-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* Chrome10+,Safari5.1+ */\n  background: -o-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* Opera 11.10+ */\n  background: -ms-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* IE10+ */\n  background: linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* W3C */\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{{\$c1}}', endColorstr='{{\$c2}}',GradientType=0 ); /* IE6-9 */\n  background-repeat: no-repeat;\n  background-attachment: fixed;\n}\ntd, th, p, ul,li,div, span {\n  font-family: Trykker, \"Lucida Grande\", Georgia, serif;\n  font-size: 12px;\n  color: {{\$font_colour}};\n}\ninput, textarea, select {\n  font-family: Trykker, \"Lucida Grande\", Georgia, serif;\n  font-size: 12px;\n}\ntd, th, p, textarea, ul,li, div, a {\n  line-height: 25px;\n}\ntable {\n  empty-cells: show;\n}\n.clear {\n  clear: both;\n}\na:link, a:visited {\n  color: {{\$link_colour}};\n  text-decoration: none; \n}\na:hover {\n  text-decoration: underline; \n}\ndiv.ui-dialog div, div.ui-dialog li, div.ui-dialog span {\n  color: #333333;\n}\n\n/* page sections */\n.ts_page {\n  margin: 40px auto;\n  position: relative;\n  text-align: left;\n}\n.ts_head_bg {\n  height: 105px;\n  border-bottom: 1px solid {{\$line}};\n  position: absolute;\n  top: 0px;\n  width: 100%;\n}\n.ts_header {\n  height: 70px;\n}\n.ts_header h1 {\n  margin: 20px 20px;\n  font-family: Trykker, \"Lucida Grande\", Georgia, serif;\n  font-weight: bold;\n  padding: 0px;\n  font-size: 30px;\n  color: {{\$header_colour}};\n  {{if \$header_shadow == \"on\"}}text-shadow: 2px 2px 5px #555555;{{/if}}\n}\nh2 {\n  font-size: 21px;\n  font-family: Trykker, \"Lucida Grande\", Georgia, serif;\n  color: {{\$page_heading}};\n}\n\n/* navigation */\n#ts_css_nav {\n  width: 180px;\n  float: left;\n  list-style: none;\n  padding: 20px;\n  margin: 0px;\n}\n#ts_css_nav li {\n  list-style: none;\n  margin: 0px 0px 2px;\n  color: #666666;\n  font-size: 12px;\n  line-height: 20px;\n  text-align: left;\n}\n#ts_css_nav div {\n  color: {{\$nav_next_page}}; \n}\n#ts_css_nav li div, #ts_css_nav li a {\n  padding: 5px 0px 5px 12px;\n  display: block;\n}\nul#ts_css_nav li.completed_page {\n  -webkit-border-radius: 4px;\n  -moz-border-radius: 4px;\n  border-radius: 4px;\n  border: 1px solid {{\$nav_prev_page_border}};\n}\nul#ts_css_nav li.completed_page div {\n  color: {{\$nav_prev_page_text}};\n}  \nul#ts_css_nav li.css_nav_current_page {\n  -webkit-border-radius: 4px;\n  -moz-border-radius: 4px;\n  border-radius: 4px;\n  border: 1px solid #ffffff;\n}\nul#ts_css_nav li.css_nav_current_page div {\n  background-color: #222222;\n  color: white;\n  margin: 1px;\n  -webkit-border-radius: 3px;\n  -moz-border-radius: 3px;\n  border-radius: 3px;\n}\nul#ts_css_nav li a {\n  margin: 1px;\n  -webkit-border-radius: 3px;\n  -moz-border-radius: 3px;\n  border-radius: 3px;\n}\nul#ts_css_nav li a:link, ul#ts_css_nav li a:visited {\n  text-decoration: none;\n  color: {{\$nav_prev_page_text}};\n  -webkit-border-radius: 2px;\n  -moz-border-radius: 2px;\n  border-radius: 2px;\n}\nul#ts_css_nav li a:hover {\n  color: white;\n  background-color: {{\$nav_prev_page_bg_over}};\n  -webkit-border-radius: 3px;\n  -moz-border-radius: 3px;\n  border-radius: 3px;\n}\n\n/* notifications */\n.notify {\n  border: 1px solid #336699;\n  background-color: #ffffee;\n  color: #336699;\n  padding: 8px;\n  width: 400px;\n}\n.notify li { color: #336699; }\n.error {\n  font-size: 8pt;\n  border: 1px solid #cc0000;\n  background-color: #ffffee;\n  color: #cc0000;\n  padding: 8px;\n  width: 550px;\n}\n.error span {\n  color: #cc0000;\n  font-weight: bold;\n  margin-bottom: 4px;\n}\n\n/* forms */\ntable.table_1 > tbody > tr > td {\n  border-bottom: 1px solid {{\$fieldset_lines}};\n}\ntable.table_1 > tbody > tr > td.rowN {\n  border-bottom: none;\n}\n.req {\n  color: #aa0000;  \n}\n.fb_error {\n  background-color: #FFFFCC;\n  border: 1px solid #CC0000;\n  color: #CC0000;\n  margin-bottom: 12px;\n  padding: 8px;\n}\n\n/* for the code / markup editor */\n.editor {\n  background-color: white;\n  border: 1px solid #999999;\n  padding: 3px;\n}\n.ts_page_content {\n  width: 720px;\n  float: right;\n}\nfieldset {\n  border: 1px solid {{\$fieldset_lines}};\n  font-size: 11pt;\n  font-weight: bold;\n  color: {{\$fieldset_colour}};\n  margin-bottom: 10px;\n  background-color: {{\$fieldset_bg}};\n}\n{{if \$fieldset_shadow == \"on\"}}\nfieldset legend {\n  text-shadow: 2px 2px 3px #333333;\n}\n{{/if}}\n.ts_continue_button input {\n  background-color: #222222;\n  color: white;\n  padding: 2px 10px;\n  border-radius: 3px;\n  border: 0px;\n  cursor: pointer;\n}\n.ts_continue_button input:hover {\n  background-color: {{\$submit_btn_over}};\n}\n#ts_footer {\n  border-top: 1px solid {{\$line}};\n  padding: 20px;\n  color: #222222;\n}\n.edit_link {\n  text-shadow: none; \n  margin-left: 12px;\n}\n.edit_link a {\n  color: {{\$edit_link_colour}};\n  text-decoration: none;\n}\n.edit_link a:hover {\n  text-decoration: underline;\n}\n\n#form_builder__edit_link {\n  position: absolute;\n  right: 5px;\n  top: 5px;\n  padding: 2px 10px;\n  background-color: black;\n  border: 1px solid white;\n  color: white;\n  text-decoration: none;\n  border-radius: 4px;\n}\n#form_builder__edit_link:hover {\n  color: #06a4ff; \n}\n",
      "last_updated"  => "2012-01-18 23:06:16"
    )
  ),

  // placeholders
  "placeholders" => array(
    array(
      "placeholder_label" => "Colours",
      "placeholder"       => "colours",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Orange",
      "options" => array(
        array("option_text" => "Red"),
        array("option_text" => "Orange"),
        array("option_text" => "Green"),
        array("option_text" => "Blue"),
        array("option_text" => "Black"),
        array("option_text" => "Grey"),
        array("option_text" => "White")
      )
    ),
    array(
      "placeholder_label" => "Footer HTML",
      "placeholder"       => "footer_html",
      "field_type"        => "textarea",
      "field_orientation" => "horizontal",
      "default_value"     => "",
      "options" => array()
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "Theme - Default",
  "version"     => "1.0",
  "description" => "A form template set based on the same styles as the default Form Tools user interface. Complete with choice of swatches!",
  "is_complete" => "yes",
  "list_order"  => 5,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/global/css/main.css\">\n  {{\$required_resources}}\n  {{\$R.styles}}\n  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/themes/default/css/styles.css\">\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/themes/default/css/swatch_{{\$P.swatch|lower|regex_replace:'/[ ]/':'_'}}.css\">\n</head>\n<body>\n  <div id=\"container\">\n    <div id=\"header\">\n      {{form_builder_edit_link}}\n      <h1>{{\$form_name|upper}}</h1>\n    </div>\n\n"
    ),
    array(
      "template_type" => "header",
      "template_name" => "No Header",
      "content"       => "<html>\n<head>\n  <title>{{\$form_name}}</title>\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/global/css/main.css\">\n  {{\$required_resources}}\n  {{\$R.styles}}\n  <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/themes/default/css/styles.css\">\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/themes/default/css/swatch_{{\$P.swatch|lower|regex_replace:'/[ ]/':'_'}}.css\">\n</head>\n<body>\n  <div id=\"container\">\n      {{form_builder_edit_link}}\n"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "      </div>\n    </td>\n  </tr>\n  </table>\n\n</div>\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "<div id=\"content\">\n  <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n  <tr>\n    <td width=\"180\" valign=\"top\">\n      <div id=\"left_nav\">\n        {{navigation}}\n      </div>\n    </td>\n    <td valign=\"top\">\n      <div style=\"width:740px\">\n        <div class=\"title margin_bottom_large\">{{\$page_name}}</div>\n\n        {{error_message}}\n\n        <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n          id=\"ts_form_element_id\" name=\"edit_submission_form\">\n        {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n          {{assign var=group value=\$curr_group.group}}\n          {{assign var=fields value=\$curr_group.fields}}\n\n        <a name=\"s{{\$group.group_id}}\"></a>\n        {{if \$group.group_name}}\n        <div class=\"subtitle underline margin_bottom_large\">{{\$group.group_name|upper}}</div>\n        {{/if}}\n\n        {{if \$fields|@count > 0}}\n        <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n          border=\"0\" width=\"688\">\n        {{/if}}\n    \n        {{foreach from=\$fields item=curr_field name=i}}\n          {{assign var=field_id value=\$field.field_id}}\n          <tr>\n            <td width=\"180\" valign=\"top\" class=\"pad_left_small\">\n              {{\$curr_field.field_title}}\n              <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n            </td>\n            <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n              {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n              settings=\$settings submission_id=\$submission_id}}\n            </td>\n          </tr>\n        {{/foreach}}\n\n        {{if \$fields|@count > 0}}\n          </table>  \n        {{/if}}\n\n      {{/foreach}}\n\n      {{continue_block}}\n\n      </form>\n\n      </div>\n    </td>\n  </tr>\n  </table>\n</div>\n"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "<div id=\"content\">\n  <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n  <tr>\n    <td width=\"180\" valign=\"top\">\n      <div id=\"left_nav\">\n        {{navigation}}\n      </div>\n    </td>\n    <td valign=\"top\">\n      <div style=\"width:740px\">\n        <div class=\"title margin_bottom_large\">{{\$review_page_title}}</div>\n\n        <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n        {{foreach from=\$grouped_fields item=curr_group}}\n          {{assign var=group value=\$curr_group.group}}\n          {{assign var=fields value=\$curr_group.fields}}\n\n          {{if \$fields|@count > 0}}\n            <div class=\"subtitle underline margin_bottom_large\">\n              {{\$group.group_name|upper|default:\"&nbsp;\"}}\n              <span class=\"edit_link\">\n                <a href=\"?page={{\$group.custom_data}}#s{{\$group.group_id}}\">EDIT</a>\n              </span>\n            </div>\n\n            <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n              border=\"0\" width=\"668\">\n          {{/if}}\n\n          {{foreach from=\$fields item=curr_field name=i}}\n            {{assign var=field_id value=\$field.field_id}}\n            <tr>\n              <td class=\"pad_left_small\" width=\"200\" valign=\"top\">{{\$curr_field.field_title}}</td>\n              <td valign=\"top\">\n                {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n                  field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n              </td>\n            </tr>\n          {{/foreach}}\n\n          {{if \$fields|@count > 0}}\n            </table>    \n          {{/if}}\n \n        {{/foreach}}\n\n        {{continue_block}}\n\n      </form>\n\n      </div>\n    </td>\n  </tr>\n  </table>\n</div>\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "<div id=\"content\">\n  <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n  <tr>\n    <td width=\"180\" valign=\"top\">\n      <div id=\"left_nav\">\n        {{navigation}}\n      </div>\n    </td>\n    <td valign=\"top\">\n      <div style=\"width:740px\">\n      {{\$thankyou_page_content}} \n      </div>\n    </td>\n  </tr>\n  </table>\n</div>\n\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "<div id=\"content\">\n  {{\$form_offline_page_content}}\n</div>"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Button Only",
      "content"       => "<div class=\"ts_continue_button\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation",
      "content"       => "<ul id=\"ts_css_nav\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n  \n  {{if \$current_page > \$i && \$current_page != \$num_pages}}\n  \n  <li class=\"nav_link_submenu completed_page\"><a href=\"?page={{\$i}}\">{{\$page_info.page_name}}</a></li>\n  \n  {{elseif \$current_page == \$i}}\n  \n  <li class=\"css_nav_current_page\"><div>{{\$page_info.page_name}}</div></li>\n    {{else}}\n  <li><div>{{\$page_info.page_name}}</div></li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Numbered",
      "content"       => "<ul id=\"ts_css_nav\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{if \$current_page > \$i && \$current_page != \$num_pages}}\n    <li class=\"nav_link_submenu completed_page\"><a href=\"?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n    {{elseif \$current_page == \$i}}\n    <li class=\"css_nav_current_page\"><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n    {{else}}\n    <li><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n    {{/if}}\n  {{/foreach}}\n</ul>"
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"ft_message error margin_bottom_large\">\n    <div style=\"padding:8px\">\n      {{\$validation_error}}\n    </div>\n  </div>\n{{/if}}\n\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "Additional Styles",
      "placeholder"   => "styles",
      "content"       => "/**\n * The majority of styles for this Template Set are pulled directly from the Core's default theme.\n * This supplements them for a few things that aren't covered.\n */\nh1 {\n  margin: 0px;\n  padding: 28px 0px 0px 21px;\n  float: left;\n  font-family: 'Lato', Arial;\n  color: white;\n  font-size: 20px;\n  font-weight: normal;\n}\n#ts_css_nav {\n  list-style:none;\n  margin: 0px;\n  padding: 0px; \n}\n#ts_css_nav li {\n  height: 27px;\n}\n#ts_css_nav li a, #ts_css_nav li div {\n  padding: 2px 0px 2px 4px;\n  width: 150px;\n}\n#ts_css_nav li.completed_page a:link, #ts_css_nav li.completed_page a:visited {\n  display: block;\n  text-underline: none;\n}\n#ts_css_nav li.css_nav_current_page div {\n  font-weight: bold;\n}\n.edit_link {\n  float: right;\n}\n.edit_link a:link, .edit_link a:visited {\n  padding: 0px 7px;\n  background-color: #aaaaaa;\n  color: white;\n  border-radius: 3px;\n  letter-spacing: 0px;\n} \n.edit_link a:hover {\n  background-color: #222222;\n  text-decoration: none;\n}\n#form_builder__edit_link {\n  background-color: #444444;\n  border-radius: 3px 3px 3px 3px;\n  color: white;\n  float: right;\n  margin: 25px;\n  padding: 0 8px;\n}\n#form_builder__edit_link:hover {\n  background-color: #000000; \n  text-decoration: none;\n}\n.ts_heading {\n  font: 17.6px/20px Verdana,sans-serif;\n  padding-bottom: 5px;\n  margin: 0px;\n}\n",
      "last_updated"  => "2012-02-03 17:47:10"
    )
  ),

  // placeholders
  "placeholders" => array(
    array(
      "placeholder_label" => "Swatch",
      "placeholder"       => "swatch",
      "field_type"        => "select",
      "field_orientation" => "na",
      "default_value"     => "Orange",
      "options" => array(
        array("option_text" => "Aquamarine"),
        array("option_text" => "Blue"),
        array("option_text" => "Dark Blue"),
        array("option_text" => "Green"),
        array("option_text" => "Grey"),
        array("option_text" => "Light Brown"),
        array("option_text" => "Orange"),
        array("option_text" => "Purple"),
        array("option_text" => "Red"),
        array("option_text" => "Yellow")
      )
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "Theme - Classic Grey",
  "version"     => "1.0",
  "description" => "A form template set based on the same styles as the default Form Tools user interface. Complete with choice of swatches!",
  "is_complete" => "yes",
  "list_order"  => 6,

  // templates
  "templates" => array(
    array(
      "template_type" => "page_layout",
      "template_name" => "Page Layout",
      "content"       => "{{header}}\n{{page}}\n{{footer}}"
    ),
    array(
      "template_type" => "header",
      "template_name" => "Header",
      "content"       => "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html dir=\"ltr\">\n<head>\n  <title>{{\$form_name}}</title>\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/global/css/main.css\">\n  {{\$required_resources}}\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/global/css/main.css\">\n  <link type=\"text/css\" rel=\"stylesheet\" href=\"{{\$g_root_url}}/themes/classicgrey/css/styles.css\">  \n  {{\$R.styles}}\n</head>\n<body>\n<div id=\"container\">\n  <div id=\"header\">\n    {{form_builder_edit_link}}\n    <h1>{{\$form_name}}</h1>\n  </div>\n  <div id=\"header_row\">\n    <div id=\"left_nav_top\">\n    <div style=\"height: 20px\"> </div>\n    </div>\n  </div>\n\n  <div class=\"outer\">\n    <div class=\"inner\">\n      <div class=\"float-wrap\">\n      <div id=\"content\">\n        <div class=\"content_wrap\">\n          <div id=\"main_window\">\n            <div id=\"page_content\">\n\n"
    ),
    array(
      "template_type" => "footer",
      "template_name" => "Footer",
      "content"       => "              </div>\n            </div>\n          </div>\n        </div>\n        <div id=\"left\" class=\"pad_top_large\">\n          {{navigation}}\n        </div>\n      </div>\n      <div class=\"clear\"></div>\n    </div>\n  </div>\n</div>\n\n</body>\n</html>"
    ),
    array(
      "template_type" => "form_page",
      "template_name" => "Form Page",
      "content"       => "        <div class=\"title margin_bottom_large\">{{\$page_name}}</div>\n\n        {{error_message}}\n\n        <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n          id=\"ts_form_element_id\" name=\"edit_submission_form\">\n        {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n          {{assign var=group value=\$curr_group.group}}\n          {{assign var=fields value=\$curr_group.fields}}\n\n        <a name=\"s{{\$group.group_id}}\"></a>\n        {{if \$group.group_name}}\n        <div class=\"subtitle underline margin_bottom_large\">{{\$group.group_name|upper}}</div>\n        {{/if}}\n\n        {{if \$fields|@count > 0}}\n        <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n          border=\"0\" width=\"688\">\n        {{/if}}\n    \n        {{foreach from=\$fields item=curr_field name=i}}\n          {{assign var=field_id value=\$field.field_id}}\n          <tr>\n            <td width=\"180\" valign=\"top\" class=\"pad_left_small\">\n              {{\$curr_field.field_title}}\n              <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n            </td>\n            <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n              {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n              settings=\$settings submission_id=\$submission_id}}\n            </td>\n          </tr>\n        {{/foreach}}\n\n        {{if \$fields|@count > 0}}\n          </table>  \n        {{/if}}\n\n      {{/foreach}}\n\n      {{continue_block}}\n\n      </form>\n\n\n"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "<div class=\"title margin_bottom_large\">{{\$review_page_title}}</div>\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n  {{foreach from=\$grouped_fields item=curr_group}}\n    {{assign var=group value=\$curr_group.group}}\n    {{assign var=fields value=\$curr_group.fields}}\n\n    {{if \$fields|@count > 0}}\n      <div class=\"subtitle underline margin_bottom_large\">\n        {{\$group.group_name|upper|default:\"&nbsp;\"}} \n        <span class=\"edit_link\">\n          <a href=\"?page={{\$group.custom_data}}#s{{\$group.group_id}}\">EDIT</a>\n        </span>\n      </div>\n\n      <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n        border=\"0\" width=\"668\">\n    {{/if}}\n\n    {{foreach from=\$fields item=curr_field name=i}}\n      {{assign var=field_id value=\$field.field_id}}\n      <tr>\n        <td class=\"pad_left_small\" width=\"200\" valign=\"top\">{{\$curr_field.field_title}}</td>\n        <td valign=\"top\">\n          {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n            field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n        </td>\n      </tr>\n    {{/foreach}}\n\n    {{if \$fields|@count > 0}}\n      </table>    \n    {{/if}}\n \n  {{/foreach}}\n\n  {{continue_block}}\n\n</form>\n"
    ),
    array(
      "template_type" => "thankyou_page",
      "template_name" => "Thankyou Page",
      "content"       => "{{\$thankyou_page_content}} \n\n"
    ),
    array(
      "template_type" => "form_offline_page",
      "template_name" => "Form Offline Page",
      "content"       => "{{\$form_offline_page_content}}"
    ),
    array(
      "template_type" => "continue_block",
      "template_name" => "Continue - Button Only",
      "content"       => "<div class=\"ts_continue_button\">\n  <input type=\"submit\" name=\"form_tools_continue\" value=\"Continue\" />\n</div>"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation",
      "content"       => "{{if \$page_type != \"form_offline_page\"}}\n  <ul id=\"ts_css_nav\">\n    {{foreach from=\$nav_pages item=page_info name=row}}\n      {{assign var=i value=\$smarty.foreach.row.iteration}}\n \n      {{if \$current_page > \$i && \$current_page != \$num_pages}}\n        <li class=\"nav_link_submenu completed_page\"><a \n          href=\"?page={{\$i}}\">{{\$page_info.page_name}}</a></li>\n      {{elseif \$current_page == \$i}}\n        <li class=\"css_nav_current_page\"><div>{{\$page_info.page_name}}</div></li>\n      {{else}}\n        <li><div>{{\$page_info.page_name}}</div></li>\n      {{/if}}\n    {{/foreach}}\n  </ul>\n{{/if}}\n"
    ),
    array(
      "template_type" => "navigation",
      "template_name" => "Navigation - Numbered",
      "content"       => "{{if \$page_type != \"form_offline_page\"}}\n  <ul id=\"ts_css_nav\">\n    {{foreach from=\$nav_pages item=page_info name=row}}\n      {{assign var=i value=\$smarty.foreach.row.iteration}}\n \n      {{if \$current_page > \$i && \$current_page != \$num_pages}}\n        <li class=\"nav_link_submenu completed_page\"><a\n          href=\"?page={{\$i}}\">{{\$i}}. {{\$page_info.page_name}}</a></li>\n      {{elseif \$current_page == \$i}}\n        <li class=\"css_nav_current_page\"><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n      {{else}}\n        <li><div>{{\$i}}. {{\$page_info.page_name}}</div></li>\n      {{/if}}\n    {{/foreach}}\n  </ul>\n{{/if}}"
    ),
    array(
      "template_type" => "error_message",
      "template_name" => "Error Message",
      "content"       => "{{if \$validation_error}}\n  <div class=\"ft_message error margin_bottom_large\">\n    <div style=\"padding:8px\">\n      {{\$validation_error}}\n    </div>\n  </div>\n{{/if}}\n\n"
    )
  ),

  // resources
  "resources" => array(
    array(
      "resource_type" => "css",
      "resource_name" => "Additional Styles",
      "placeholder"   => "styles",
      "content"       => "/**\n * The majority of styles for this Template Set are pulled directly from the Classic Grey theme.\n * This supplements them for a few things that aren't covered.\n */\n#ts_css_nav {\n  list-style:none;\n  margin: 0px;\n  padding: 0px; \n}\n#ts_css_nav li {\n  height: 27px;\n}\n#ts_css_nav li a, #ts_css_nav li div {\n  padding: 4px 0px 4px 12px;\n  width: 188px;\n  border-bottom: 1px dotted #aaaaaa;\n}\n#ts_css_nav li.completed_page a:link, #ts_css_nav li.completed_page a:visited {\n  display: block;\n  text-underline: none;\n}\n#ts_css_nav li.css_nav_current_page div {\n  font-weight: bold;\n}\n.ts_heading {\n  font-size: 16px; \n  margin: 0px 0px 15px;\n}\n.edit_link {\n  float: right;\n}\n.edit_link a:link, .edit_link a:visited {\n  padding: 0px 7px;\n  background-color: #aaaaaa;\n  color: white;\n  border-radius: 3px;\n  letter-spacing: 0px;\n} \n.edit_link a:hover {\n  background-color: #222222;\n  text-decoration: none;\n}\n#form_builder__edit_link {\n  background-color: #444444;\n  border-radius: 3px 3px 3px 3px;\n  color: white;\n  float: right;\n  margin: 25px;\n  padding: 0 8px;\n}\n#form_builder__edit_link:hover {\n  background-color: #000000; \n  text-decoration: none;\n}\n#header {\n  background: #000000;\n  background: -moz-linear-gradient(left,  #000000 1%, #5b5b5b 100%);\n  background: -webkit-gradient(linear, left top, right top, color-stop(1%,#000000), color-stop(100%,#5b5b5b));\n  background: -webkit-linear-gradient(left,  #000000 1%,#5b5b5b 100%);\n  background: -o-linear-gradient(left,  #000000 1%,#5b5b5b 100%);\n  background: -ms-linear-gradient(left,  #000000 1%,#5b5b5b 100%);\n  background: linear-gradient(left,  #000000 1%,#5b5b5b 100%);\n  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#000000', endColorstr='#5b5b5b',GradientType=1 );\n}\n#header h1 {\n  margin: 0px;\n  padding: 21px;\n  color: white;\n  font-size: 20px;\n  font-weight: normal; \n}\n",
      "last_updated"  => "2012-02-03 12:57:51"
    )
  ),

  // placeholders
  "placeholders" => array()
);
