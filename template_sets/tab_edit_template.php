<?php

$template_id = ft_load_module_field("form_builder", "template_id", "template_id");

if (isset($request["update_template"]))
{
  list($g_success, $g_message) = fb_update_template($request);
}

$template_info = fb_get_template($template_id);
$set_id = $template_info["set_id"];
$template_set_info = fb_get_template_set($set_id);


$ordered_template_ids = fb_get_template_ids($set_id);
$previous_template_link = "<span class=\"light_grey\">{$LANG["word_previous_leftarrow"]}</span>";
$next_template_link = "<span class=\"light_grey\">{$LANG["word_next_rightarrow"]}</span>";
$num_templates = count($ordered_template_ids);

$same_page = ft_get_clean_php_self();
for ($i=0; $i<$num_templates; $i++)
{
  $curr_template_id = $ordered_template_ids[$i];
  if ($curr_template_id == $template_id)
  {
    if ($i != 0)
    {
      $previous_template_id = $ordered_template_ids[$i-1];
      $previous_template_link = "<a href=\"{$same_page}?page=edit_template&set_id=$set_id&template_id=$previous_template_id\">{$LANG["word_previous_leftarrow"]}</a>";
    }
    if ($i != $num_templates - 1)
    {
      $next_template_id = $ordered_template_ids[$i+1];
      $next_template_link = "<a href=\"{$same_page}?page=edit_template&set_id=$set_id&template_id=$next_template_id\">{$LANG["word_next_rightarrow"]}</a>";
    }
  }
}

// override the form nav links so that it always links to the Views page
$page_vars["prev_tabset_link"] = (!empty($links["prev_set_id"])) ? "index.php?page=templates&set_id={$links["prev_set_id"]}" : "";
$page_vars["next_tabset_link"] = (!empty($links["next_set_id"])) ? "index.php?page=templates&set_id={$links["next_set_id"]}" : "";

$page_vars["previous_template_link"] = $previous_template_link;
$page_vars["next_template_link"] = $next_template_link;
$page_vars["template_info"] = $template_info;
$page_vars["template_set_info"] = $template_set_info;
$page_vars["js_messages"] = array("word_close", "word_yes", "word_no");
$page_vars["module_js_messages"] = array("text_template_set_complete", "phrase_template_set_status", "text_template_set_incomplete");
$page_vars["resources"] = fb_get_resources($set_id);
$page_vars["placeholders"] = fb_get_placeholders($set_id);
$page_vars["head_string"] =<<< END
  <script src="../global/scripts/manage_template_sets.js"></script>
  <script src="$g_root_url/global/codemirror/js/codemirror.js"></script>
  <link type="text/css" rel="stylesheet" href="$g_root_url/modules/form_builder/global/css/styles.css"></link>
END;

$page_vars["head_js"] =<<< END
$(function() {
  $("#toggle_placeholders_link").bind("click", function() {
    if ($("#placeholders_section").css("display") != "block") {
      $("#placeholders_section").show("blind");
    } else {
      $("#placeholders_section").hide("blind");
    }
    return false;
  });
  fb_ns.init_template_status_dialog();
});
END;

ft_display_module_page("templates/template_sets/index.tpl", $page_vars);
