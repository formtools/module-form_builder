$(function() {
  $(".publish_tab_offline_date").bind("click", function() {
    var content = g.mod_messages.notify_form_offline_message.replace(/%offline_time%/, this.innerHTML);
    content = content.replace(/%current_time%/, g.current_server_time);

    ft.create_dialog({
      title:   g.mod_messages.phrase_form_offline_date,
      content: content,
      width:   460,
      buttons: [{
    	text: g.messages["word_close"],
    	click: function() { $(this).dialog("close"); }
      }]
    });
  });
});

var fb_ns = {};
fb_ns.delete_form_configuration_dialog = $("<div></div>");


/**
 * Called when the user selects a form from one of the dropdowns in the first column. It shows
 * the appropriate View content in the second column.
 */
fb_ns.select_form = function(form_id) {
  if (form_id == "") {
    $("#view_id")[0].options.length = 0;
    $("#view_id")[0].options[0] = new Option(g.messages["phrase_please_select_form"], "");
    $("#view_id").attr("disabled", "disabled");
    return false;
  } else {
    $("#view_id").attr("disabled", "");
    fb_ns.populate_view_dropdown("view_id", form_id);
  }
  return false;
}


/**
 * Populates a dropdown element with a list of Views including a "Please Select" default
 * option.
 */
fb_ns.populate_view_dropdown = function(element_id, form_id) {
  var form_index = null;
  for (var i=0; i<page_ns.form_views.length; i++) {
    if (form_id == page_ns.form_views[i][0]) {
      form_index = i;
    }
  }
  $("#" + element_id)[0].options.length = 0;

  for (var i=0; i<page_ns.form_views[form_index][1].length; i++) {
    var view_id   = page_ns.form_views[form_index][1][i][0];
    var view_name = page_ns.form_views[form_index][1][i][1];
    $("#" + element_id)[0].options[i+1] = new Option(view_name, view_id);
  }
}


fb_ns.delete_form_configuration = function(el) {
  var row_group         = $(el).closest(".row_group");
  var published_form_id = row_group.find(".sr_order").val();
  var is_published      = row_group.find(".is_published").val();

  var content = "";
  if (is_published == "no") {
    ft.create_dialog({
      dialog:     fb_ns.delete_form_configuration_dialog,
      title:      g.messages["phrase_please_confirm"],
      content:    $("#confirm_delete_form_configuration_not_published").html(),
      popup_type: "warning",
      width:      450,
      buttons: [
        {
          text: g.messages["word_yes"],
          click: function() {
            window.location = 'edit.php?page=publish&delete=' + published_form_id;
          }
        },
        {
          text: g.messages["word_no"],
          click: function() { $(this).dialog("close"); }
        }
      ]
    });
  } else {
    ft.create_dialog({
      dialog:     $("#confirm_delete_form_configuration_published"),
      title:      g.messages["phrase_please_confirm"],
      popup_type: "warning",
      width:      450,
      buttons: [
        {
          text: g.messages["word_yes"],
          click: function() {
        	var delete_form_config = ($("#delete_form_config").attr("checked")) ? "yes" : "no";
            window.location = 'edit.php?page=publish&delete_published_form=' + published_form_id + "&delete_form_config=" + delete_form_config;
          }
        },
        {
          text: g.messages["word_no"],
          click: function() { $(this).dialog("close"); }
        }
      ]
    });
  }

  return false;
}

