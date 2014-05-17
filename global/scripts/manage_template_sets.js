var fb_ns = {};
fb_ns.delete_template_set_dialog = $("<div></div>");
fb_ns.delete_template_dialog = $("<div></div>");

// relating the placeholder management
fb_ns.current_field_type = null;
fb_ns.num_placeholder_rows = 0;


fb_ns.init_create_new_template_set_dialog = function() {
  $("#create_new_template_set").bind("click", function() {
    ft.create_dialog({
      dialog: $("#create_new_template_set_dialog"),
      title:  g.messages["phrase_create_new_template_set"],
      width: 400,
      open: function() {
        $("#create_error").html("").hide();
      },
      buttons: [
        {
          text: g.messages["phrase_create_new_template_set"],
          click: function() {
            var template_set_name = $.trim($("#new_template_name").val());
            var original_set_id   = ($("#original_set_id").length > 0) ? $("#original_set_id").val() : "";
            if (!template_set_name) {
              ft.display_message("create_error", 0, g.messages["validation_no_template_set_name"]);
              return;
            }
            $.ajax({
              url: g.root_url + "/modules/form_builder/global/code/actions.php",
              dataType: "json",
              type: "POST",
              data: {
                action:            "create_new_template_set",
                template_set_name: template_set_name,
                original_set_id:   original_set_id
              },
              success: function(data) {
                if (data.success == 1) {
                  window.location = "template_sets/index.php?page=info&set_id=" + data.message;
                } else {
                  ft.display_message("ft_message", false, data.message);
                  $("#create_new_template_set_dialog").dialog("close");
                }
              }
            });
          }
        },
        {
          text: g.messages["word_close"],
          click: function() {
          $(this).dialog("close");
          }
        }
      ]
    });
  });
}


fb_ns.delete_template_set = function(el) {
  var set_id = $(el).closest(".row_group").find(".sr_order").val();
  ft.create_dialog({
    dialog:  fb_ns.delete_template_set_dialog,
    title:   g.messages["phrase_please_confirm"],
    content: g.messages["confirm_delete_template_set"],
    popup_type: "warning",
    buttons: [
      {
        text:  g.messages["word_yes"],
        click: function() {
          window.location = "index.php?&delete=" + set_id;
        }
      },
      {
        text:  g.messages["word_no"],
        click: function() {
          $(this).dialog("close");
        }
      }
    ]
  });
  return false;
}


fb_ns.init_create_new_template_dialog = function() {

  // these two event handlers just ensure the right radio is checked depending on the select box they're using
  $("#source_template_id").bind("change", function() { $("#nts1").attr("checked", "checked"); });

  // (we have to use a class here, since there
  $(".has_templates_new_template_dropdown").bind("change", function() { $("#nts2").attr("checked", "checked"); });

  $("#create_new_template").bind("click", function() {
    ft.create_dialog({
      dialog: $("#create_new_template_dialog"),
      title:  g.messages["phrase_create_new_template"],
      width: 520,
      open: function() {
        $("#create_error").html("").hide();
      },
      buttons: [
        {
          text: g.messages["phrase_create_new_template"],
          click: function() {
            var template_name = $.trim($("#new_template_name").val());
            var template_type = $.trim($("#new_template_type").val());
            var source_template_id = "";
            var has_templates = $("#has_templates").val(); // yes / no
            var new_template_source = "";
            var errors = [];
            if (!template_name) {
              errors.push("&bull; " + g.messages["validation_no_template_name"]);
            }
            if (has_templates == "yes") {
              new_template_source = $("input[name=new_template_source]:checked").val();
              if (new_template_source == "existing_template") {
                source_template_id = $("#source_template_id").val();
                if (!source_template_id) {
                  errors.push("&bull; " + g.messages["validation_no_source_template"]);
                }
              } else {
                if (!template_type) {
                  errors.push("&bull; " + g.messages["validation_no_template_type"]);
                }
              }
            } else {
              if (!template_type) {
                errors.push("&bull; " + g.messages["validation_no_template_type"]);
              }
            }

            if (errors.length) {
              var error_str = errors.join("<br />");
              ft.display_message("create_error", 0, error_str);
              return;
            }

            $.ajax({
              url: g.root_url + "/modules/form_builder/global/code/actions.php",
              dataType: "json",
              type: "POST",
              data: {
                action: "create_new_template",
                set_id:        $("#set_id").val(),
                has_templates: has_templates,
                template_name: template_name,
                template_type: template_type,
                source_template_id: source_template_id,
                new_template_source: new_template_source
              },
              success: function(data) {
                if (data.success == 1) {
                  window.location = "index.php?page=edit_template&template_id=" + data.message;
                } else {

                }
              }
            });
          }
        },
        {
          text: g.messages["word_close"],
          click: function() { $(this).dialog("close"); }
        }
      ]
    });
  });
}



/**
 * This relies on the fb.current_field_type having been set in the page.
 */
fb_ns.change_field_type = function(choice) {
  if (choice == fb_ns.current_field_type) {
    return;
  }
  if (choice == "radios" || choice == "checkboxes" || choice == "select" || choice == "multi-select") {
    if ($("#field_options_div")[0].style.display == "none") {
      $("#field_options_div").slideDown(200);
    }
    if (choice == "radios" || choice == "checkboxes") {
      $("#fo1, #fo2").attr("disabled", "");
      $("#fo3").attr("disabled", "disabled");
      if ($("#fo3").attr("checked")) {
        $("#fo1").attr("checked", "checked");
      }
    } else {
      $("#fo1, #fo2").attr("disabled", "disabled");
      $("#fo3").attr("disabled", "");
      $("#fo3").attr("checked", "checked");
    }
  } else {
    if ($("#field_options_div")[0].style.display != "none") {
      $("#field_options_div").slideUp(200);
    }
  }

  fb_ns.current_field_type = choice;
}


/**
 * This function is called in the Add Form process, and on the Edit Form -> main tab. It dynamically
 * adds rows to the "Form URLs" section, letting the user add as many page URLs as their form contains.
 */
fb_ns.add_placeholder_row = function() {
  var curr_row = ++fb_ns.num_placeholder_rows;

  var li1 = $("<li class=\"col1 sort_col\"></li>");
  var li2 = $("<li class=\"col2\"><input type=\"text\" name=\"placeholder_options[]\" /></li>");
  var li3 = $("<li class=\"col3 colN del\"></li>");
  var ul  = $("<ul></ul>").append(ft.group_nodes([li1, li2, li3]));

  var hidden_sort_field = $("<input type=\"hidden\" value=\"1\" class=\"sr_order\">");
  var clr = $("<div class=\"clear\"></div>");
  var row_group = $("<div class=\"row_group\"></div>").append(ft.group_nodes([hidden_sort_field, ul, clr]));

  var html = sortable_ns.get_sortable_row_markup({row_group: row_group, is_grouped: false });

  $(".placeholder_option_list .rows").append(html);
  sortable_ns.reorder_rows($(".placeholder_option_list"), true);

  return false;
}


fb_ns.delete_template = function(el) {
  ft.create_dialog({
    title:      g.messages["phrase_please_confirm"],
    content:    g.messages["confirm_delete_template"],
    popup_type: "warning",
    buttons: [{
      text: g.messages["word_yes"],
      click: function() {
        var resource_id = $(el).closest(".row_group").find(".sr_order").val();
        window.location = "index.php?page=templates&delete=" + resource_id;
      }
    },
    {
      text: g.messages["word_no"],
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
  return false;
}


fb_ns.init_add_resource = function(template_id) {
  $("#add_resource").bind("click", function() {
    ft.create_dialog({
      dialog: $("#add_resource_dialog"),
      title:  "Add New Resource",
      width: 560,
      open: function() {
        $("#create_error").html("").hide();
      },
      buttons: [
        {
          text: "Add Resource",
          click: function() {
            var resource_name = $.trim($("#resource_name").val());
            if (!resource_name) {
              ft.display_message("create_error", 0, "Please enter the resource name.");
              return;
            }
            $.ajax({
              url: g.root_url + "/modules/form_builder/global/code/actions.php",
              dataType: "json",
              type: "POST",
              data: {
                action: "add_resource",
                set_id: $("#set_id").val(),
                resource_name: resource_name,
                placeholder: $("#placeholder").val(),
                resource_type: $("input[name=resource_type]:checked").val()
              },
              success: function(data) {
                if (data.success == 1) {
                  window.location = "?page=edit_resource&resource_id=" + data.message;
                } else {

                }
              }
            });
          }
        },
        {
          text: g.messages["word_cancel"],
          click: function() { $(this).dialog("close"); }
        }
      ]
    });
  });
}


fb_ns.delete_resource = function(el) {
  ft.create_dialog({
    title:      g.messages["phrase_please_confirm"],
    content:    g.messages["confirm_delete_resource"],
    popup_type: "warning",
    buttons: [{
      text: g.messages["word_yes"],
      click: function() {
        var resource_id = $(el).closest(".row_group").find(".sr_order").val();
        window.location = "index.php?page=resources&delete=" + resource_id;
      }
    },
    {
      text: g.messages["word_no"],
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
  return false;
}


fb_ns.delete_placeholder = function(el) {
  ft.create_dialog({
    title:      g.messages["phrase_please_confirm"],
    content:    g.messages["confirm_delete_placeholder"],
    popup_type: "warning",
    buttons: [{
      text: g.messages["word_yes"],
      click: function() {
        var resource_id = $(el).closest(".row_group").find(".sr_order").val();
        window.location = "index.php?page=placeholders&delete=" + resource_id;
      }
    },
    {
      text: g.messages["word_no"],
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
  return false;
}


/**
 * Called on each of the Edit Template Set pages, this binds a dialog window to the "Complete" / "Incomplete"
 * labels that appear at the top right. The dialog explains what that means, and what information is missing
 * in order for the template set to become usable.
 */
fb_ns.init_template_status_dialog = function() {
  $(".template_set_marker").bind("click", fb_ns.display_template_status_dialog);
}

fb_ns.display_template_status_dialog = function(e) {
  var content = "";
  if ($(e.target).hasClass("template_set_complete")) {
    content = g.messages["text_template_set_complete"];
  } else {
    content = g.messages["text_template_set_incomplete"];
  }
  ft.create_dialog({
    title:      g.messages["phrase_template_set_status"],
    content:    content,
    width:      460,
    popup_type: "info",
    buttons: [{
      text: g.messages["word_close"],
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
  return false;
}

