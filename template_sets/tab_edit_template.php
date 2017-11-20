<?php

use FormTools\General as CoreGeneral;
use FormTools\Modules;
use FormTools\Modules\FormBuilder\Placeholders;
use FormTools\Modules\FormBuilder\Resources;
use FormTools\Modules\FormBuilder\Templates;
use FormTools\Modules\FormBuilder\TemplateSets;

$template_id = Modules::loadModuleField("form_builder", "template_id", "template_id");

if (isset($request["update_template"])) {
    list($g_success, $g_message) = Templates::updateTemplate($request, $L);
}

$template_info = Templates::getTemplate($template_id);
$set_id = $template_info["set_id"];
$template_set_info = TemplateSets::getTemplateSet($set_id);


$ordered_template_ids = TemplateSets::getTemplateIds($set_id);
$previous_template_link = "<span class=\"light_grey\">{$LANG["word_previous_leftarrow"]}</span>";
$next_template_link = "<span class=\"light_grey\">{$LANG["word_next_rightarrow"]}</span>";
$num_templates = count($ordered_template_ids);

$same_page = CoreGeneral::getCleanPhpSelf();
for ($i = 0; $i < $num_templates; $i++) {
    $curr_template_id = $ordered_template_ids[$i];
    if ($curr_template_id == $template_id) {
        if ($i != 0) {
            $previous_template_id = $ordered_template_ids[$i - 1];
            $previous_template_link = "<a href=\"{$same_page}?page=edit_template&set_id=$set_id&template_id=$previous_template_id\">{$LANG["word_previous_leftarrow"]}</a>";
        }
        if ($i != $num_templates - 1) {
            $next_template_id = $ordered_template_ids[$i + 1];
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
$page_vars["module_js_messages"] = array(
"text_template_set_complete",
"phrase_template_set_status",
"text_template_set_incomplete"
);
$page_vars["resources"] = Resources::getResources($set_id);
$page_vars["placeholders"] = Placeholders::getPlaceholders($set_id);
$page_vars["head_js"] = <<< END
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

$module->displayPage("templates/template_sets/index.tpl", $page_vars);
