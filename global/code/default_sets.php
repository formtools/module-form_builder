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
  "version"     => "1.1",
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
      "content"       => "{{navigation}}\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n  <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n    <a name=\"s{{\$group.group_id}}\"></a>\n  {{if \$group.group_name}}\n    <h3>{{\$group.group_name}}</h3>\n  {{else}}\n    <br />\n  {{/if}}\n\n  {{if \$fields|@count > 0}}\n  <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n  {{/if}}\n    \n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"180\" valign=\"top\">\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </td>\n      <td class=\"answer\" valign=\"top\">\n        <div class=\"pad_left\">\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n        </div>\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>  \n  {{/if}}\n\n{{/foreach}}\n\n{{continue_block}}\n\n</form>"
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
      "content"       => "body {
  text-align: center;
  padding: 0px;
  margin: 0px;
  background-color: #efefef;
}
td, th, p, input, textarea, select,ul,li,div, span {
  font-family: \"Lucida Grande\",\"Lucida Sans Unicode\", Tahoma, sans-serif;
  font-size: 12px;
  color: #555555;
}
td, th, p, textarea, ul,li, div {
  line-height: 22px;
}
a:link, a:visited {
  color: #336699;
}
table {
  empty-cells: show;
}

/* page sections */
.ts_page:after {
  -moz-transform: translate(0pt, 0pt);
  background: none repeat scroll 0 0 transparent;
  border-radius: 20px 20px 20px 20px;
  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);
  content: \"\";
  left: 0;
  position: absolute;
  width: 100%;
  z-index: -2;
}
.ts_page {
  margin: 40px auto;
  position: relative;
  text-align: left;
}
.ts_header {
  background: none repeat scroll 0 0 rgba(0, 0, 0, 0.5);
  border: 3px solid #CCCCCC;
  height: 140px;
  background: #3a3a3a; /* Old browsers */
  background: -moz-linear-gradient(45deg,  #777777 0%, #999999 100%); /* FF3.6+ */
  background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#777777), color-stop(100%,#999999)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(45deg,  #777777 0%,#999999 100%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(45deg,  #777777 0%,#999999 100%); /* Opera 11.10+ */
  background: -ms-linear-gradient(45deg,  #777777 0%,#999999 100%); /* IE10+ */
  background: linear-gradient(45deg,  #777777 0%,#999999 100%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#777777', endColorstr='#999999',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);
}
.ts_header h1 {
  margin: 56px 50px;
  padding: 0px;
  font-size: 20px;
  color: white;
}
.ts_content {
  background-color: white;
  border: 1px solid #CCCCCC;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
  padding: 25px 50px;
}
.ts_continue_block {
  margin-top: 16px;
  background-color: #ffffdd;
  padding: 8px;
  box-shadow:1px 2px 2px #878787;
}
.ts_continue_block input {
  float: right;
}
.ts_continue_button {
  margin-top: 12px;
}
.highlighted_cell {
  color: #990000;
  background-color: #ffffee;
  text-align: center;
}
.light_grey {
  color: #999999;
}
h2 {
  font-size: 24px;
}
h3 {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  -webkit-border-top-left-radius: 4px;
  -webkit-border-top-right-radius: 4px;
  -moz-border-radius-topleft: 4px;
  -moz-border-radius-topright: 4px;
  font-size: 12px;
  font-weight: normal;
  margin-bottom: 0;
  margin-right: 1px;
  padding: 1px 0 0 5px;
  width: 792px;
  height: 22px;
}
h3 a:link, h3 a:visited {
  background-color: white;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  color: black;
  float: right;
  line-height: 17px;
  margin-right: 3px;
  margin-top: 2px;
  padding: 0 8px;
  text-decoration: none;
}
h3 a:hover {
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}

/* navigation */
ul#css_nav {
  clear: both;
  width:100%;
  margin: 0px;
  padding: 0px;
  overflow: hidden;
}
ul#css_nav li {
  float: left;
  background-color: #efefef;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  list-style: none;
  text-align:center;
  margin: 0px 2px 20px 0px;
  color: #666666;
  font-size: 11px;
  line-height: 20px;
}
ul#css_nav li span {
  font-size: 11px;
  line-height: 20px;
}

ul#css_nav li.css_nav_current_page {
  background-color: #999999;
  color: white;
}
ul#css_nav li a:link, ul#css_nav li a:visited {
  display: block;
  text-decoration: none;
  color: white;
  background-color: #999999;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}
ul#css_nav li a:hover {
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}
.nav_1_pages li {
  width: 100%;
}
.nav_2_pages li {
  width: 49.7%;
}
.nav_3_pages li {
  width: 33%;
}
.nav_4_pages li {
  width: 24.7%;
}
.nav_5_pages li {
  width: 19.7%;
}
.nav_6_pages li {
  width: 16.4%;
}
.nav_7_pages li {
  width: 14%;
}
.nav_8_pages li {
  width: 12.2%;
}

/* notifications */
.notify {
  border: 1px solid #336699;
  background-color: #ffffee;
  color: #336699;
  padding: 8px;
  width: 400px;
}
.notify li { color: #336699; }
.error {
  font-size: 8pt;
  border: 1px solid #cc0000;
  background-color: #ffffee;
  color: #cc0000;
  padding: 8px;
  width: 550px;
}
.error span {
  color: #cc0000;
  font-weight: bold;
  margin-bottom: 4px;
}

/* forms */
table.table_1 > tbody > tr > td {
  border-bottom: 1px solid #dddddd;
}
.table_1_bg td {
  padding: 1px;
  padding-left: 1px;
  background-color: #336699;
  border-bottom: 1px solid #cccccc;
}
td.answer {
  background-color: #efefef;
}
.pad_left {
  padding-left: 4px;
}
.req {
  color: #aa0000;
}
.fb_error {
  border: 1px solid #990000;
  padding: 8px;
  background-color: #ffefef;
}

/* for the code / markup editor */
.editor {
  background-color: white;
  border: 1px solid #999999;
  padding: 3px;
}


/* - - - \"Highlight Colour\" placeholder conditional CSS - - -*/
{{if \$P.colours == \"Red\"}}
h3 {
  background-color: #cc3131;
  color: white;
}
ul#css_nav li a:hover {
  background-color: #861e1e;
}
h3 a:hover {
  background-color: #fac1c1;
  color: black;
}
{{elseif \$P.colours == \"Orange\"}}
h3 {
  background-color: #ff9c00;
  color: white;
}
ul#css_nav li a:hover {
  background-color: #4c3512;
}
h3 a:hover {
  background-color: #ffefd5;
  color: black;
}
{{elseif \$P.colours == \"Yellow\"}}
h3 {
  background-color: #FAEC0C;
  color: #777777;
}
ul#css_nav li a:hover {
  background-color: #595900;
}
h3 a:hover {
  background-color: #444000;
  color: #ffffcc;
}
{{elseif \$P.colours == \"Green\"}}
h3 {
  background-color: #009211;
  color: white;
}
ul#css_nav li a:hover {
  background-color: #004608;
}
h3 a:hover {
  background-color: #daf4dd;
  color: black;
}
{{elseif \$P.colours == \"Blue\"}}
h3 {
  background-color: #2969c9;
  color: white;
}
h3 a:hover {
  background-color: #a6c8f0;
  color: black;
}
ul#css_nav li a:hover {
  background-color: #1e4580;
}
{{elseif \$P.colours == \"Grey\"}}
h3 {
  background-color: #777777;
  color: white;
}
ul#css_nav li a:hover {
  background-color: #333333;
}
h3 a:hover {
  background-color: #222222;
  color: white;
}
{{else}}
h3 {
  background-color: #6D8AAC;
  color: white;
}
ul#css_nav li a:hover {
  background-color: #2e425a;
}
h3 a:hover {
  background-color: #c9e2ff;
  color: black;
}
{{/if}}
",
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
  "version"     => "1.2",
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
      "content"       => "{{navigation}}\n\n{{form_builder_edit_link}}\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\" \n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n  <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n  \n  <a name=\"s{{\$group.group_id}}\"></a>\n  {{if \$group.group_name}}\n    <h3>{{\$group.group_name|upper}}</h3>\n  {{else}}\n    <br />\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n\n    <ul class=\"ts_field\">\n      <li class=\"ts_field_label\">\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </li>\n      <li>\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n      </li>\n    </ul>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    <br />\n  {{/if}}\n{{/foreach}}\n\n{{continue_block}}\n    \n</form>"
    ),
    array(
      "template_type" => "review_page",
      "template_name" => "Review Page",
      "content"       => "{{navigation}}\n\n{{form_builder_edit_link}}\n\n<h2>{{\$review_page_title}}</h2>\n\n<p>\n  Please review the information below to confirm it is correct. If you need to edit any\n  values, just click the EDIT link at the top right of the section.\n</p>\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\">\n{{foreach from=\$grouped_fields item=curr_group}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  {{if \$fields|@count > 0}}\n  <h3><a href=\"?page={{\$group.custom_data|default:1}}#s{{\$group.group_id}}\">EDIT</a>{{\$group.group_name|upper}}</h3>\n \n    <table class=\"ts_review_table\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\">\n  {{/if}}\n\n  {{foreach from=\$fields item=curr_field}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td valign=\"top\" width=\"200\">{{\$curr_field.field_title}}</td>\n      <td valign=\"top\">\n        {{edit_custom_field form_id=\$form_id submission_id=\$submission_id\n          field_info=\$curr_field field_types=\$field_types settings=\$settings}}\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>\n    \n    <br />\n  {{/if}}\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n"
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
      "content"       => "{{if \$P.colours == \"Blue\"}}
  {{assign var=header_bg value=\"#388ef4\"}}
  {{assign var=border_colour value=\"#C4DFFF\"}}
  {{assign var=selected_row_bg value=\"#d5e8ff\"}}
  {{assign var=selected_row_bottom value=\"#d5e8ff\"}}
  {{assign var=content_border value=\"#94c5fe\"}}
  {{assign var=continue_block_colour value=\"#f1f7ff\"}}
{{elseif \$P.colours == \"Green\"}}
  {{assign var=header_bg value=\"#0b9c00\"}}
  {{assign var=border_colour value=\"#e7ffe5\"}}
  {{assign var=selected_row_bg value=\"#d9f4cb\"}}
  {{assign var=selected_row_bottom value=\"#d9f4cb\"}}
  {{assign var=content_border value=\"#ade0aa\"}}
  {{assign var=continue_block_colour value=\"#E9F9E7\"}}
{{elseif \$P.colours == \"Purple\"}}
  {{assign var=header_bg value=\"#ac52ce\"}}
  {{assign var=border_colour value=\"#f7e0ff\"}}
  {{assign var=selected_row_bg value=\"#f6dfff\"}}
  {{assign var=selected_row_bottom value=\"#f6dfff\"}}
  {{assign var=content_border value=\"#e9c1f8\"}}
  {{assign var=continue_block_colour value=\"#ffffcc\"}}
{{elseif \$P.colours == \"Orange\"}}
  {{assign var=header_bg value=\"#ffa904\"}}
  {{assign var=border_colour value=\"#ffa904\"}}
  {{assign var=selected_row_bg value=\"#ffd789\"}}
  {{assign var=selected_row_bottom value=\"#ffa904\"}}
  {{assign var=content_border value=\"#CE911A\"}}
  {{assign var=continue_block_colour value=\"#f1f7ff\"}}
{{/if}}

body {
  text-align: center;
  padding: 0px;
  margin: 0px;
  background-color: #999999;
}
td, th, p, input, textarea, select,ul,li,div, span {
  font-family: {{\$P.font}};
  font-size: {{\$P.font_size}};
  color: #555555;
}
td, th, p, textarea, ul,li, div {
  line-height: 22px;
}
a:link, a:visited {
  color: #336699;
}
table {
  empty-cells: show;
}
#form_builder__edit_link {
  float: right;
}
.req {
  color: #aa0000;
}
.fb_error {
  margin-top: 16px;
  padding: 8px;
  box-shadow: 1px 2px 2px #878787;
  background-color: #ffefef;
}


/* page sections */
.ts_page:after {
  -moz-transform: translate(0pt, 0pt);
  background: none repeat scroll 0 0 transparent;
  border-radius: 20px 20px 20px 20px;
  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);
  content: \"\";
  left: 0;
  position: absolute;
  width: 100%;
  z-index: -2;
}
.ts_page {
  margin: 20px auto 0px;
  background: #ccc;
  position:relative;
  box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);
  -moz-box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);
  -webkit-box-shadow: 1px 6px 11px rgba(0, 0, 0, 0.36);
  text-align: left;
  border: 5px solid {{\$border_colour}};
}
.ts_header {
  background: none repeat scroll 0 0 rgba(0, 0, 0, 0.5);
  border: 3px solid #CCCCCC;
  height: 140px;
  background: #3a3a3a; /* Old browsers */
  background: -moz-linear-gradient(45deg,  #777777 0%, #999999 100%); /* FF3.6+ */
  background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#3a3a3a), color-stop(100%,#4f4f4f)); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* Opera 11.10+ */
  background: -ms-linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* IE10+ */
  background: linear-gradient(45deg,  #3a3a3a 0%,#4f4f4f 100%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#3a3a3a', endColorstr='#4f4f4f',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
  box-shadow: 0 1px 12px rgba(0, 0, 0, 0.1);
}
.ts_header h1 {
  margin: 56px 50px;
  padding: 0px;
  font-size: 20px;
  color: white;
}
.ts_content {
  background-color: white;
  box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
  padding: 25px 50px;
  border: 1px solid {{\$content_border}};
}
.ts_continue_block {
  margin-top: 16px;
  padding: 8px;
  box-shadow: 1px 2px 2px #878787;
  background-color: {{\$continue_block_colour}};
}
.ts_continue_block input {
  float: right;
}
.ts_field_row_selected {
  background-color: {{\$selected_row_bg}};
  border-bottom: 1px solid {{\$selected_row_bottom}};
}
.ts_continue_button {
  margin-top: 12px;
}
.highlighted_cell {
  color: #990000;
  background-color: #ffffee;
  text-align: center;
}
.light_grey {
  color: #999999;
}
.ts_field {
  border-bottom: 1px solid #efefef;
  padding: 10px 6px 15px;
  list-style: none;
  margin: 0px;
}
.ts_review_table td {
  border-bottom: 1px solid #efefef;
  padding: 3px 5px 2px;
}
h2 {
  font-size: 24px;
}
h3 {
  background-color: {{\$header_bg}};
  color: white;
  font-size: 12px;
  font-weight: normal;
  margin-bottom: 0;
  padding: 1px 0 0 5px;
  height: 22px;
}
h3 a:link, h3 a:visited {
  background-color: white;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  color: black;
  float: right;
  line-height: 17px;
  margin-right: 3px;
  margin-top: 2px;
  padding: 0 8px;
  text-decoration: none;
}
h3 a:hover {
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}

/* navigation */
ul#css_nav {
  clear: both;
  width:100%;
  margin: 0px;
  padding: 0px;
  overflow: hidden;
}
ul#css_nav li {
  float: left;
  background-color: #efefef;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  list-style: none;
  text-align:center;
  margin: 0px 2px 20px 0px;
  color: #666666;
  font-size: 11px;
  line-height: 20px;
}
ul#css_nav li.css_nav_current_page {
  background-color: #999999;
  color: white;
}
ul#css_nav li a:link, ul#css_nav li a:visited {
  display: block;
  text-decoration: none;
  color: white;
  background-color: #999999;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}
ul#css_nav li a:hover {
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}
.nav_1_pages li {
  width: 100%;
}
.nav_2_pages li {
  width: 50%;
}
.nav_3_pages li {
  width: 33%;
}
.nav_4_pages li {
  width: 24.7%;
}
.nav_5_pages li {
  width: 19.5%;
}
.nav_6_pages li {
  width: 16%;
}
.nav_7_pages li {
  width: 13%;
}
.nav_8_pages li {
  width: 12%;
}

/* notifications */
.notify {
  border: 1px solid #336699;
  background-color: #ffffee;
  color: #336699;
  padding: 8px;
  width: 400px;
}
.notify li { color: #336699; }
.error {
  font-size: 8pt;
  border: 1px solid #cc0000;
  background-color: #ffffee;
  color: #cc0000;
  padding: 8px;
  width: 550px;
}
.error span {
  color: #cc0000;
  font-weight: bold;
  margin-bottom: 4px;
}

/* for the code / markup editor */
.editor {
  background-color: white;
  border: 1px solid #999999;
  padding: 3px;
}
ul#css_nav li a:hover {
  background-color: #2e425a;
}
h3 a:hover {
  background-color: #c9e2ff;
  color: black;
}

",
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
  "version"     => "1.2",
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
      "content"       => "{{navigation}}\n\n<div class=\"ts_content\">\n  <div class=\"ts_content_inner\">\n\n  <h2>{{\$page_name}}</h2>\n\n  {{error_message}}\n\n  <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n    id=\"ts_form_element_id\" name=\"edit_submission_form\">\n    <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n  {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n    {{assign var=group value=\$curr_group.group}}\n    {{assign var=fields value=\$curr_group.fields}}\n\n      <a name=\"s{{\$group.group_id}}\"></a>\n    {{if \$group.group_name}}\n      <h3>{{\$group.group_name}}</h3>\n    {{else}}\n      <br />\n    {{/if}}\n\n    {{if \$fields|@count > 0}}\n    <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"798\">\n    {{/if}}\n    \n    {{foreach from=\$fields item=curr_field}}\n      {{assign var=field_id value=\$field.field_id}}\n      <tr>\n        <td width=\"180\" valign=\"top\">\n          {{\$curr_field.field_title}}\n          <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n        </td>\n        <td class=\"answer\" valign=\"top\">\n          <div class=\"pad_left\">\n          {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n            settings=\$settings submission_id=\$submission_id}}\n          </div>\n        </td>\n      </tr>\n    {{/foreach}}\n\n    {{if \$fields|@count > 0}}\n      </table>  \n    {{/if}}\n\n  {{/foreach}}\n\n  {{continue_block}}\n\n  </form>\n    \n  </div>\n</div>\n"
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
      "content"       => "<div class=\"ts_content\">\n  <div class=\"ts_content_inner\">\n    {{\$form_offline_page_content}}\n  </div>\n</div>\n"
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
      "content"       => "<ul id=\"css_nav\" class=\"nav_{{\$nav_pages|@count}}_pages\">\n  {{foreach from=\$nav_pages item=page_info name=row}}\n    {{assign var=i value=\$smarty.foreach.row.iteration}}\n    {{assign var=a value=\" "
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
      "content"       => "html {
  height: 100%;
}
body {
  height: 100%;
  text-align: center;
  padding: 0px;
  margin: 0px;
  background: rgb(106,147,184);
  background: -moz-linear-gradient(top,  rgba(106,147,184,1) 0%, rgba(115,151,183,1) 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(106,147,184,1)), color-stop(100%,rgba(115,151,183,1)));
  background: -webkit-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);
  background: -o-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);
  background: -ms-linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);
  background: linear-gradient(top,  rgba(106,147,184,1) 0%,rgba(115,151,183,1) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#6a93b8', endColorstr='#7397b7',GradientType=0 );
  background-repeat: no-repeat;
  background-attachment: fixed;
}
td, th, p, input, textarea, select,ul,li,div, span {
  font-family: \"Lucida Grande\",\"Lucida Sans Unicode\", Tahoma, sans-serif;
  font-size: 12px;
  color: #555555;
}
td, th, p, textarea, ul, li, div {
  line-height: 22px;
}
a:link, a:visited {
  color: #336699;
}
table {
  empty-cells: show;
}

/* page sections */
.ts_page:after {
  -moz-transform: translate(0pt, 0pt);
  background: none repeat scroll 0 0 transparent;
  border-radius: 20px 20px 20px 20px;
  box-shadow: 15px 0 30px rgba(0, 0, 0, 0.2);
  content: \"\";
  left: 0;
  position: absolute;
  width: 100%;
  z-index: -2;
}
.ts_page {
  margin: 40px auto;
  position: relative;
  text-align: left;
}
.ts_header h1 {
  margin: 0px 0px 42px 20px;
  padding: 0px;
  font-size: {{\$P.font_size}};
  color: white;
  font-family: \"{{\$P.font}}\", \"Lucida Grande\", Arial;
  font-weight: normal;
}
.ts_footer {
  background: rgb(64,86,107);
  background: -moz-linear-gradient(top,  rgb(64,86,107) 0%, rgb(44,61,76) 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(64,86,107)), color-stop(100%,rgb(44,61,76)));
  background: -webkit-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: -o-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: -ms-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#40566b', endColorstr='#2c3d4c',GradientType=0 );
  -webkit-border-bottom-left-radius: 6px;
  -webkit-border-bottom-right-radius: 6px;
  -moz-border-radius-bottomleft: 6px;
  -moz-border-radius-bottomright: 6px;
  border-bottom-left-radius: 6px;
  border-bottom-right-radius: 6px;
  padding: 10px 0px;
  text-align: center;
  color: #dddddd;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
  height: 5px;
}
.ts_content {
  background-color: white;
  border: 1px solid #777777;
  border-top: 0px;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.3);
  padding: 25px 50px;
}
.ts_continue_block {
  margin-top: 16px;
  background-color: #ffffdd;
  padding: 8px;
  box-shadow: 1px 2px 2px #878787;
}
.ts_continue_block input {
  float: right;
}
.ts_continue_button {
  margin-top: 12px;
}
.light_grey {
  color: #999999;
}
h2 {
  font-size: 20px;
}
.ts_heading {
  font-size: 20px;
}

h3 {
  border-top-left-radius: 4px;
  border-top-right-radius: 4px;
  -webkit-border-top-left-radius: 4px;
  -webkit-border-top-right-radius: 4px;
  -moz-border-radius-topleft: 4px;
  -moz-border-radius-topright: 4px;
  font-size: 12px;
  font-weight: normal;
  margin-bottom: 0;
  margin-right: 1px;
  padding: 1px 0 0 5px;
  width: 792px;
  background-color: #36485a;
  color: white;
  height: 22px;
}
h3 a:link, h3 a:visited {
  background-color: white;
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
  color: black;
  float: right;
  line-height: 17px;
  margin-right: 3px;
  margin-top: 2px;
  padding: 0 8px;
  text-decoration: none;
}
h3 a:hover {
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}

/* navigation */
ul#css_nav {
  clear: both;
  margin: 0px;
  padding: 0px 40px;
  overflow: hidden;
  background: rgb(64,86,107);
  background: -moz-linear-gradient(top,  rgb(64,86,107) 0%, rgb(44,61,76) 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgb(64,86,107)), color-stop(100%,rgb(44,61,76)));
  background: -webkit-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: -o-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: -ms-linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  background: linear-gradient(top,  rgb(64,86,107) 0%,rgb(44,61,76) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#40566b', endColorstr='#2c3d4c',GradientType=0 );
  -webkit-border-top-left-radius: 6px;
  -webkit-border-top-right-radius: 6px;
  -moz-border-radius-topleft: 6px;
  -moz-border-radius-topright: 6px;
  border-top-left-radius: 6px;
  border-top-right-radius: 6px;
  height: 38px;
}
ul#css_nav li {
  float: left;
  list-style: none;
  text-align:center;
  color: #dddddd;
  font-size: 11px;
  padding: 8px 0px;
}
ul#css_nav li span {
  font-size: 11px;
}

ul#css_nav li.completed_page {
  padding: 0px;
}
ul#css_nav li.css_nav_current_page {
  background: rgb(249,249,249);
  background: -moz-linear-gradient(top, rgba(249,249,249,1) 0%, rgba(255,255,255,1) 100%);
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(249,249,249,1)), color-stop(100%,rgba(255,255,255,1)));
  background: -webkit-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);
  background: -o-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);
  background: -ms-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);
  background: linear-gradient(top,  rgba(249,249,249,1) 0%,rgba(255,255,255,1) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#ffffff',GradientType=0 );
  color: #000000;
}
ul#css_nav li a:link, ul#css_nav li a:visited, ul#css_nav li span {
  display: block;
  text-decoration: none;
  color: white;
  background-color: #333333;
  padding: 8px 0px;
  opacity: 0.5;
  filter: alpha(opacity=50);
}
 ul#css_nav li a:hover {
  background-color: #222222;
  opacity: 0.9;
  filter: alpha(opacity=90);
}

.nav_1_pages li {
  width: 150px;
}
.nav_2_pages li {
  width: 150px;
}
.nav_3_pages li {
  width: 150px;
}
.nav_4_pages li {
  width: 150px;
}
.nav_5_pages li {
  width: 150px;
}
.nav_6_pages li {
  width: 136px;
}
.nav_7_pages li {
  width: 116px;
}
.nav_8_pages li {
  width: 102px;
}


/* notifications */
.notify {
  border: 1px solid #336699;
  background-color: #ffffee;
  color: #336699;
  padding: 8px;
  width: 400px;
}
.notify li { color: #336699; }
.error {
  font-size: 8pt;
  border: 1px solid #cc0000;
  background-color: #ffffee;
  color: #cc0000;
  padding: 8px;
  width: 550px;
}
.error span {
  color: #cc0000;
  font-weight: bold;
  margin-bottom: 4px;
}

/* forms */
table.table_1 > tbody > tr > td {
  border-bottom: 1px solid #dddddd;
}
.table_1_bg td {
  padding: 1px;
  padding-left: 1px;
  background-color: #336699;
  border-bottom: 1px solid #cccccc;
}
td.answer {
  background-color: #efefef;
}
.pad_left {
  padding-left: 4px;
}
.req {
  color: #aa0000;
}
.fb_error {
  border: 1px solid #990000;
  padding: 8px;
  background-color: #ffefef;
}

/* for the code / markup editor */
.editor {
  background-color: white;
  border: 1px solid #999999;
  padding: 3px;
}
#form_builder__edit_link {
  position: absolute;
  right: 5px;
  top: 0px;
  text-decoration: none;
}
#form_builder__edit_link:hover {
  color: #990000;
  text-decoration: underline;
}

",
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
    )
  )
);

$g_default_sets[] = array(
  "set_name"    => "Illuminate",
  "version"     => "1.1",
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
      "content"       => "{{navigation}}\n\n<div class=\"ts_page_content\">\n\n<h2>{{\$page_name}}</h2>\n\n{{error_message}}\n\n<form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n  id=\"ts_form_element_id\" name=\"edit_submission_form\">\n  <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n{{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n  {{assign var=group value=\$curr_group.group}}\n  {{assign var=fields value=\$curr_group.fields}}\n\n  <a name=\"s{{\$group.group_id}}\"></a>\n  <fieldset>\n  {{if \$group.group_name}}\n    <legend>{{\$group.group_name}}</legend>\n  {{/if}}\n\n  {{if \$fields|@count > 0}}\n  <table class=\"table_1\" cellpadding=\"1\" cellspacing=\"1\" border=\"0\" width=\"688\">\n  {{/if}}\n    \n  {{foreach from=\$fields item=curr_field name=i}}\n    {{assign var=field_id value=\$field.field_id}}\n    <tr>\n      <td width=\"180\" valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n        {{\$curr_field.field_title}}\n        <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n      </td>\n      <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n        {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n          settings=\$settings submission_id=\$submission_id}}\n      </td>\n    </tr>\n  {{/foreach}}\n\n  {{if \$fields|@count > 0}}\n    </table>  \n  {{/if}}\n\n  </fieldset>\n\n{{/foreach}}\n\n{{continue_block}}\n\n</form>\n  \n</div>\n"
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
      "content"       => "{{* This top section defines the colours, based on the selected Colour placeholder *}}
{{assign var=header_colour value=\"#ffffff\"}}
{{assign var=header_shadow value=\"on\"}}
{{if \$P.colours == \"Red\"}}
  {{assign var=c1 value=\"#950000\"}}
  {{assign var=c2 value=\"#af0a0a\"}}
  {{assign var=link_colour value=\"#ffffcc\"}}
  {{assign var=line value=\"#670000\"}}
  {{assign var=fieldset_bg value=\"#780404\"}}
  {{assign var=fieldset_lines value=\"#660909\"}}
  {{assign var=fieldset_colour value=\"#FFFF99\"}}
  {{assign var=fieldset_shadow value=\"on\"}}
  {{assign var=font_colour value=\"#ffffff\"}}
  {{assign var=page_heading value=\"#000000\"}}
  {{assign var=nav_next_page value=\"#999999\"}}
  {{assign var=nav_prev_page_text value=\"#999999\"}}
  {{assign var=nav_prev_page_bg_over value=\"#D78B00\"}}
  {{assign var=nav_prev_page_border value=\"#999999\"}}
  {{assign var=submit_btn_over value=\"#ff3c00\"}}
  {{assign var=edit_link_colour value=\"#ffffff\"}}
{{elseif \$P.colours == \"Orange\"}}
  {{assign var=c1 value=\"#ffa500\"}}
  {{assign var=c2 value=\"#ffb12b\"}}
  {{assign var=link_colour value=\"#990000\"}}
  {{assign var=line value=\"#ffc558\"}}
  {{assign var=fieldset_bg value=\"#ef9c00\"}}
  {{assign var=fieldset_lines value=\"#d28900\"}}
  {{assign var=fieldset_colour value=\"#FFFF99\"}}
  {{assign var=fieldset_shadow value=\"on\"}}
  {{assign var=font_colour value=\"#333333\"}}
  {{assign var=page_heading value=\"#ffffff\"}}
  {{assign var=nav_next_page value=\"#555555\"}}
  {{assign var=nav_prev_page_text value=\"#AF8D4F\"}}
  {{assign var=nav_prev_page_bg_over value=\"#D78B00\"}}
  {{assign var=nav_prev_page_border value=\"#E69500\"}}
  {{assign var=submit_btn_over value=\"#ff3c00\"}}
  {{assign var=edit_link value=\"#990000\"}}
{{elseif \$P.colours == \"Green\"}}
  {{assign var=c1 value=\"#299a0b\"}}
  {{assign var=c2 value=\"#31a612\"}}
  {{assign var=link_colour value=\"#FFFF99\"}}
  {{assign var=line value=\"#1c7e00\"}}
  {{assign var=fieldset_bg value=\"#228a00\"}}
  {{assign var=fieldset_lines value=\"#1e7d00\"}}
  {{assign var=fieldset_colour value=\"#FFFF99\"}}
  {{assign var=fieldset_shadow value=\"on\"}}
  {{assign var=font_colour value=\"#eeeeee\"}}
  {{assign var=page_heading value=\"#333333\"}}
  {{assign var=nav_next_page value=\"#0f4f00\"}}
  {{assign var=nav_prev_page_text value=\"#136600\"}}
  {{assign var=nav_prev_page_bg_over value=\"#135205\"}}
  {{assign var=nav_prev_page_border value=\"#1e710b\"}}
  {{assign var=submit_btn_over value=\"#0093E8\"}}
  {{assign var=edit_link value=\"#990000\"}}
{{elseif \$P.colours == \"Blue\"}}
  {{assign var=c1 value=\"#0083cf\"}}
  {{assign var=c2 value=\"#0690e0\"}}
  {{assign var=link_colour value=\"#FFFF99\"}}
  {{assign var=line value=\"#0c5e8d\"}}
  {{assign var=fieldset_bg value=\"#0878b8\"}}
  {{assign var=fieldset_lines value=\"#0669a2\"}}
  {{assign var=fieldset_colour value=\"#FFFF99\"}}
  {{assign var=fieldset_shadow value=\"on\"}}
  {{assign var=font_colour value=\"#eeeeee\"}}
  {{assign var=page_heading value=\"#222222\"}}
  {{assign var=nav_next_page value=\"#333333\"}}
  {{assign var=nav_prev_page_text value=\"#efefef\"}}
  {{assign var=nav_prev_page_bg_over value=\"#3396e2\"}}
  {{assign var=nav_prev_page_border value=\"#cccccc\"}}
  {{assign var=submit_btn_over value=\"#621111\"}}
  {{assign var=edit_link value=\"#621111\"}}
{{elseif \$P.colours == \"Black\"}}
  {{assign var=c1 value=\"#222222\"}}
  {{assign var=c2 value=\"#333333\"}}
  {{assign var=link_colour value=\"#c8ebff\"}}
  {{assign var=line value=\"#444444\"}}
  {{assign var=fieldset_bg value=\"#353535\"}}
  {{assign var=fieldset_lines value=\"#444444\"}}
  {{assign var=fieldset_colour value=\"#c8ebff\"}}
  {{assign var=fieldset_shadow value=\"on\"}}
  {{assign var=font_colour value=\"#efefef\"}}
  {{assign var=page_heading value=\"#eeeeee\"}}
  {{assign var=nav_next_page value=\"#999999\"}}
  {{assign var=nav_prev_page_text value=\"#3a8ab8\"}}
  {{assign var=nav_prev_page_bg_over value=\"#3a8ab8\"}}
  {{assign var=nav_prev_page_border value=\"#4a99c7\"}}
  {{assign var=submit_btn_over value=\"#3a8ab8\"}}
  {{assign var=edit_link value=\"#c8ebff\"}}
{{elseif \$P.colours == \"Grey\"}}
  {{assign var=c1 value=\"#dddddd\"}}
  {{assign var=c2 value=\"#ffffff\"}}
  {{assign var=link_colour value=\"#0033cc\"}}
  {{assign var=line value=\"#cccccc\"}}
  {{assign var=fieldset_bg value=\"#f2f2f2\"}}
  {{assign var=fieldset_lines value=\"#aaaaaa\"}}
  {{assign var=fieldset_colour value=\"#888888\"}}
  {{assign var=fieldset_shadow value=\"off\"}}
  {{assign var=font_colour value=\"#333333\"}}
  {{assign var=page_heading value=\"#555555\"}}
  {{assign var=nav_next_page value=\"#999999\"}}
  {{assign var=nav_prev_page_text value=\"#888888\"}}
  {{assign var=nav_prev_page_bg_over value=\"#888888\"}}
  {{assign var=nav_prev_page_border value=\"#888888\"}}
  {{assign var=submit_btn_over value=\"#3a8ab8\"}}
  {{assign var=edit_link value=\"#c8ebff\"}}
{{elseif \$P.colours == \"White\"}}
  {{assign var=c1 value=\"#ffffff\"}}
  {{assign var=c2 value=\"#ffffff\"}}
  {{assign var=header_colour value=\"#222222\"}}
  {{assign var=header_shadow value=\"off\"}}
  {{assign var=link_colour value=\"#0093e8\"}}
  {{assign var=line value=\"#000000\"}}
  {{assign var=fieldset_bg value=\"#ffffff\"}}
  {{assign var=fieldset_lines value=\"#000000\"}}
  {{assign var=fieldset_colour value=\"#000000\"}}
  {{assign var=fieldset_shadow value=\"off\"}}
  {{assign var=font_colour value=\"#333333\"}}
  {{assign var=page_heading value=\"#555555\"}}
  {{assign var=nav_next_page value=\"#999999\"}}
  {{assign var=nav_prev_page_text value=\"#888888\"}}
  {{assign var=nav_prev_page_bg_over value=\"#888888\"}}
  {{assign var=nav_prev_page_border value=\"#888888\"}}
  {{assign var=submit_btn_over value=\"#0093e8\"}}
  {{assign var=edit_link value=\"#0093e8\"}}
{{/if}}
html {
  height: 100%;
  margin: 0px;
}
body {
  height: 100%;
  text-align: center;
  padding: 0px;
  margin: 0px;
  background: {{\$c2}}; /* Old browsers */
  background: -moz-linear-gradient(top, {{\$c1}} 0%, {{\$c2}} 100%); /* FF3.6+ */
  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,{{\$c1}}), color-stop(100%,{{\$c2}})); /* Chrome,Safari4+ */
  background: -webkit-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* Chrome10+,Safari5.1+ */
  background: -o-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* Opera 11.10+ */
  background: -ms-linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* IE10+ */
  background: linear-gradient(top, {{\$c1}} 0%,{{\$c2}} 100%); /* W3C */
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='{{\$c1}}', endColorstr='{{\$c2}}',GradientType=0 ); /* IE6-9 */
  background-repeat: no-repeat;
  background-attachment: fixed;
}
td, th, p, ul,li,div, span {
  font-family: Trykker, \"Lucida Grande\", Georgia, serif;
  font-size: 12px;
  color: {{\$font_colour}};
}
input, textarea, select {
  font-family: Trykker, \"Lucida Grande\", Georgia, serif;
  font-size: 12px;
}
td, th, p, textarea, ul,li, div, a {
  line-height: 25px;
}
table {
  empty-cells: show;
}
.clear {
  clear: both;
}
a:link, a:visited {
  color: {{\$link_colour}};
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
div.ui-dialog div, div.ui-dialog li, div.ui-dialog span {
  color: #333333;
}

/* page sections */
.ts_page {
  margin: 40px auto;
  position: relative;
  text-align: left;
}
.ts_head_bg {
  height: 105px;
  border-bottom: 1px solid {{\$line}};
  position: absolute;
  top: 0px;
  width: 100%;
}
.ts_header {
  height: 70px;
}
.ts_header h1 {
  margin: 20px 20px;
  font-family: Trykker, \"Lucida Grande\", Georgia, serif;
  font-weight: bold;
  padding: 0px;
  font-size: 30px;
  color: {{\$header_colour}};
  {{if \$header_shadow == \"on\"}}text-shadow: 2px 2px 5px #555555;{{/if}}
}
h2 {
  font-size: 21px;
  font-family: Trykker, \"Lucida Grande\", Georgia, serif;
  color: {{\$page_heading}};
}

/* navigation */
#ts_css_nav {
  width: 180px;
  float: left;
  list-style: none;
  padding: 20px;
  margin: 0px;
}
#ts_css_nav li {
  list-style: none;
  margin: 0px 0px 2px;
  color: #666666;
  font-size: 12px;
  line-height: 20px;
  text-align: left;
}
#ts_css_nav div {
  color: {{\$nav_next_page}};
}
#ts_css_nav li div, #ts_css_nav li a {
  padding: 5px 0px 5px 12px;
  display: block;
}
ul#ts_css_nav li.completed_page {
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  border: 1px solid {{\$nav_prev_page_border}};
}
ul#ts_css_nav li.completed_page div {
  color: {{\$nav_prev_page_text}};
}
ul#ts_css_nav li.css_nav_current_page {
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
  border: 1px solid #ffffff;
}
ul#ts_css_nav li.css_nav_current_page div {
  background-color: #222222;
  color: white;
  margin: 1px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}
ul#ts_css_nav li a {
  margin: 1px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}
ul#ts_css_nav li a:link, ul#ts_css_nav li a:visited {
  text-decoration: none;
  color: {{\$nav_prev_page_text}};
  -webkit-border-radius: 2px;
  -moz-border-radius: 2px;
  border-radius: 2px;
}
ul#ts_css_nav li a:hover {
  color: white;
  background-color: {{\$nav_prev_page_bg_over}};
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  border-radius: 3px;
}

/* notifications */
.notify {
  border: 1px solid #336699;
  background-color: #ffffee;
  color: #336699;
  padding: 8px;
  width: 400px;
}
.notify li { color: #336699; }
.error {
  font-size: 8pt;
  border: 1px solid #cc0000;
  background-color: #ffffee;
  color: #cc0000;
  padding: 8px;
  width: 550px;
}
.error span {
  color: #cc0000;
  font-weight: bold;
  margin-bottom: 4px;
}

/* forms */
table.table_1 > tbody > tr > td {
  border-bottom: 1px solid {{\$fieldset_lines}};
}
table.table_1 > tbody > tr > td.rowN {
  border-bottom: none;
}
.req {
  color: #aa0000;
}
.fb_error {
  background-color: #FFFFCC;
  border: 1px solid #CC0000;
  color: #CC0000;
  margin-bottom: 12px;
  padding: 8px;
}

/* for the code / markup editor */
.editor {
  background-color: white;
  border: 1px solid #999999;
  padding: 3px;
}
.ts_page_content {
  width: 720px;
  float: right;
}
fieldset {
  border: 1px solid {{\$fieldset_lines}};
  font-size: 11pt;
  font-weight: bold;
  color: {{\$fieldset_colour}};
  margin-bottom: 10px;
  background-color: {{\$fieldset_bg}};
}
{{if \$fieldset_shadow == \"on\"}}
fieldset legend {
  text-shadow: 2px 2px 3px #333333;
}
{{/if}}
.ts_continue_button input {
  background-color: #222222;
  color: white;
  padding: 2px 10px;
  border-radius: 3px;
  border: 0px;
  cursor: pointer;
}
.ts_continue_button input:hover {
  background-color: {{\$submit_btn_over}};
}
#ts_footer {
  border-top: 1px solid {{\$line}};
  padding: 20px;
  color: #222222;
}
.edit_link {
  text-shadow: none;
  margin-left: 12px;
}
.edit_link a {
  color: {{\$edit_link_colour}};
  text-decoration: none;
}
.edit_link a:hover {
  text-decoration: underline;
}

#form_builder__edit_link {
  position: absolute;
  right: 5px;
  top: 5px;
  padding: 2px 10px;
  background-color: black;
  border: 1px solid white;
  color: white;
  text-decoration: none;
  border-radius: 4px;
}
#form_builder__edit_link:hover {
  color: #06a4ff;
}
",
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
  "version"     => "1.1",
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
      "content"       => "<div id=\"content\">\n  <table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">\n  <tr>\n    <td width=\"180\" valign=\"top\">\n      <div id=\"left_nav\">\n        {{navigation}}\n      </div>\n    </td>\n    <td valign=\"top\">\n      <div style=\"width:740px\">\n        <div class=\"title margin_bottom_large\">{{\$page_name}}</div>\n\n        {{error_message}}\n\n        <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n          id=\"ts_form_element_id\" name=\"edit_submission_form\">\n          <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n        {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n          {{assign var=group value=\$curr_group.group}}\n          {{assign var=fields value=\$curr_group.fields}}\n\n        <a name=\"s{{\$group.group_id}}\"></a>\n        {{if \$group.group_name}}\n        <div class=\"subtitle underline margin_bottom_large\">{{\$group.group_name|upper}}</div>\n        {{/if}}\n\n        {{if \$fields|@count > 0}}\n        <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n          border=\"0\" width=\"688\">\n        {{/if}}\n    \n        {{foreach from=\$fields item=curr_field name=i}}\n          {{assign var=field_id value=\$field.field_id}}\n          <tr>\n            <td width=\"180\" valign=\"top\" class=\"pad_left_small\">\n              {{\$curr_field.field_title}}\n              <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n            </td>\n            <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n              {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n              settings=\$settings submission_id=\$submission_id}}\n            </td>\n          </tr>\n        {{/foreach}}\n\n        {{if \$fields|@count > 0}}\n          </table>  \n        {{/if}}\n\n      {{/foreach}}\n\n      {{continue_block}}\n\n      </form>\n\n      </div>\n    </td>\n  </tr>\n  </table>\n</div>\n"
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
      "content"       => "/**
 * The majority of styles for this Template Set are pulled directly from the Core's default theme.
 * This supplements them for a few things that aren't covered.
 */
h1 {
  margin: 0px;
  padding: 28px 0px 0px 21px;
  float: left;
  font-family: 'Lato', Arial;
  color: white;
  font-size: 20px;
  font-weight: normal;
}
#ts_css_nav {
  list-style:none;
  margin: 0px;
  padding: 0px;
}
#ts_css_nav li {
  height: 27px;
}
#ts_css_nav li a, #ts_css_nav li div {
  padding: 2px 0px 2px 4px;
  width: 150px;
}
#ts_css_nav li.completed_page a:link, #ts_css_nav li.completed_page a:visited {
  display: block;
  text-underline: none;
}
#ts_css_nav li.css_nav_current_page div {
  font-weight: bold;
}
.edit_link {
  float: right;
}
.edit_link a:link, .edit_link a:visited {
  padding: 0px 7px;
  background-color: #aaaaaa;
  color: white;
  border-radius: 3px;
  letter-spacing: 0px;
}
.edit_link a:hover {
  background-color: #222222;
  text-decoration: none;
}
#form_builder__edit_link {
  background-color: #444444;
  border-radius: 3px 3px 3px 3px;
  color: white;
  float: right;
  margin: 25px;
  padding: 0 8px;
}
#form_builder__edit_link:hover {
  background-color: #000000;
  text-decoration: none;
}
.ts_heading {
  font: 17.6px/20px Verdana,sans-serif;
  padding-bottom: 5px;
  margin: 0px;
}
",
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
  "version"     => "1.1",
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
      "content"       => "        <div class=\"title margin_bottom_large\">{{\$page_name}}</div>\n\n        {{error_message}}\n\n        <form action=\"{{\$page_url}}\" method=\"post\" enctype=\"multipart/form-data\"\n          id=\"ts_form_element_id\" name=\"edit_submission_form\">\n          <input type=\"hidden\" id=\"form_tools_published_form_id\" value=\"{{\$published_form_id}}\" />\n        {{foreach from=\$grouped_fields key=k item=curr_group name=row}}\n          {{assign var=group value=\$curr_group.group}}\n          {{assign var=fields value=\$curr_group.fields}}\n\n        <a name=\"s{{\$group.group_id}}\"></a>\n        {{if \$group.group_name}}\n        <div class=\"subtitle underline margin_bottom_large\">{{\$group.group_name|upper}}</div>\n        {{/if}}\n\n        {{if \$fields|@count > 0}}\n        <table class=\"list_table margin_bottom_large\" cellpadding=\"1\" cellspacing=\"1\" \n          border=\"0\" width=\"688\">\n        {{/if}}\n    \n        {{foreach from=\$fields item=curr_field name=i}}\n          {{assign var=field_id value=\$field.field_id}}\n          <tr>\n            <td width=\"180\" valign=\"top\" class=\"pad_left_small\">\n              {{\$curr_field.field_title}}\n              <span class=\"req\">{{if \$curr_field.is_required}}*{{/if}}</span>\n            </td>\n            <td valign=\"top\" {{if \$smarty.foreach.i.last}}class=\"rowN\"{{/if}}>\n              {{edit_custom_field form_id=\$form_id field_info=\$curr_field field_types=\$field_types\n              settings=\$settings submission_id=\$submission_id}}\n            </td>\n          </tr>\n        {{/foreach}}\n\n        {{if \$fields|@count > 0}}\n          </table>  \n        {{/if}}\n\n      {{/foreach}}\n\n      {{continue_block}}\n\n      </form>\n\n\n"
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
      "content"       => "/**
 * The majority of styles for this Template Set are pulled directly from the Classic Grey theme.
 * This supplements them for a few things that aren't covered.
 */
#ts_css_nav {
  list-style:none;
  margin: 0px;
  padding: 0px;
}
#ts_css_nav li {
  height: 27px;
}
#ts_css_nav li a, #ts_css_nav li div {
  padding: 4px 0px 4px 12px;
  width: 188px;
  border-bottom: 1px dotted #aaaaaa;
}
#ts_css_nav li.completed_page a:link, #ts_css_nav li.completed_page a:visited {
  display: block;
  text-underline: none;
}
#ts_css_nav li.css_nav_current_page div {
  font-weight: bold;
}
.ts_heading {
  font-size: 16px;
  margin: 0px 0px 15px;
}
.edit_link {
  float: right;
}
.edit_link a:link, .edit_link a:visited {
  padding: 0px 7px;
  background-color: #aaaaaa;
  color: white;
  border-radius: 3px;
  letter-spacing: 0px;
}
.edit_link a:hover {
  background-color: #222222;
  text-decoration: none;
}
#form_builder__edit_link {
  background-color: #444444;
  border-radius: 3px 3px 3px 3px;
  color: white;
  float: right;
  margin: 25px;
  padding: 0 8px;
}
#form_builder__edit_link:hover {
  background-color: #000000;
  text-decoration: none;
}
#header {
  background: #000000;
  background: -moz-linear-gradient(left,  #000000 1%, #5b5b5b 100%);
  background: -webkit-gradient(linear, left top, right top, color-stop(1%,#000000), color-stop(100%,#5b5b5b));
  background: -webkit-linear-gradient(left,  #000000 1%,#5b5b5b 100%);
  background: -o-linear-gradient(left,  #000000 1%,#5b5b5b 100%);
  background: -ms-linear-gradient(left,  #000000 1%,#5b5b5b 100%);
  background: linear-gradient(left,  #000000 1%,#5b5b5b 100%);
  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#000000', endColorstr='#5b5b5b',GradientType=1 );
}
#header h1 {
  margin: 0px;
  padding: 21px;
  color: white;
  font-size: 20px;
  font-weight: normal;
}
",
      "last_updated"  => "2012-02-03 12:57:51"
    )
  ),

  // placeholders
  "placeholders" => array()
);
