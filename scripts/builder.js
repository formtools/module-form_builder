/**
 * This contains all the JS code for the Form Builder window.
 */

$(function() {
  builder_js.submit_form();

  // this handles the window resizing
  builder_js.update_window_size();
  $(window).bind("resize", builder_js.update_window_size);

  // make a note of the current selected template set
  builder_js.current_set_id = $("#template_set_id").val();

  // tot up the templates in this template set and display them in the section header
  builder_js.display_section_heading_count("templates");

  $("#pages li").live("click", builder_js.select_page);
  $("#view_id, #is_online, #irp").bind("change", function() {
    builder_js.update_page_links(builder_js.current_page);
  });

  $("#major_error span.close").bind("click", function() { window.close(); });
  $("#major_error span.goto_form_builder").bind("click", function() {
    window.opener.location = g.root_url + "/modules/form_builder/";
    window.opener.focus();
    window.close();
  });

  // closing the window should always refresh the parent in case things changed
  $(".close_window").bind("click", { close: true }, builder_js.close_window);
  $(window).bind("beforeunload", builder_js.close_window);

  $(".apply_btn").bind("click", builder_js.submit_form);
  $(".help_btn").bind("click", builder_js.show_help_dialog);
  $(".save_btn").bind("click", function() { builder_js.save_settings("save_button"); });
  $(".publish_btn").bind("click", builder_js.show_publish_dialog);
  $(".publish_settings_btn").bind("click", builder_js.show_publish_settings_dialog);

  // when the user clicks on one of the sidebar headings, it hides/shows that section
  $("#sidebar h2").bind("click", function() {
    var options_id = $(this).attr("id").replace(/_heading$/, "");
    if ($("#" + options_id).css("display") != "block") {
      $("#" + options_id).show("blind");
    } else {
      $("#" + options_id).hide("blind");
    }
  });

  $("#thankyou_page_edit_full_screen").bind("click", function() { builder_js.full_screen_editor("thankyou_page"); });
  $("#form_offline_page_edit_full_screen").bind("click", function() { builder_js.full_screen_editor("form_offline_page"); });

  builder_js.init_sidebar_toggle();
  builder_js.update_page_links(builder_js.current_page);

  $("#template_set_id").bind("change", builder_js.select_template_set);
  $("#preview_iframe").bind("load", builder_js.page_loaded);

  builder_js.init_dialogs();

  $("#fod").bind("change", function() {
    if ($(this).val() && $("#clear_offline_form").css("display") == "none") {
      $("#clear_offline_form").fadeIn(200);
    }
  }).datetimepicker({ minDate: 0 });
  $("#fod_icon_id").bind("click", function() { $.datepicker._showDatepicker($("#fod")[0]); });

  $("#clear_offline_form").bind("click", function() { $("#fod").val(""); $(this).fadeOut(200); });
});


var builder_js = {};
builder_js.current_page = 1;
builder_js.sidebar_visible = true;
builder_js.sidebar_animating = false;
builder_js.has_selected_new_view = false; // if a user changes the View, the page needs refreshing
builder_js.template_set_cache = {}; // stores the template + placeholder sections


/**
 * Submits the form and starts the swirly.
 */
builder_js.submit_form = function() {
  if (!$("#is_online").attr("checked")) {
    $("#pages ul li:nth-child(1)").addClass("selected");
  }

  // check that the page currently in memory is within range. The user may have been on the Thankyou page then
  // unchecked the Review page
  var page = parseInt($("#page").val(), 10);
  var new_total_pages = builder_js.get_num_pages();

  if (page > new_total_pages) {
    builder_js.update_nav(new_total_pages, false);
  } else if (page == new_total_pages) {
    builder_js.update_nav(new_total_pages, false);
  }

  builder_js.start_page_load();
  $("#f").submit();
}


/**
 * Called on page load and whenever a user changes the View. It updates the iframe header row which
 * provides a list of clickable links for each page in the form.
 */
builder_js.update_page_links = function(selected_page) {
  var html = "<ul>";
  if (!$("#is_online").attr("checked")) {
    html += "<li>" + g.messages["phrase_form_offline_page"] + "</li>";
  } else {
    var view_id = $("#view_id").val();

    for (var i=0; i<g.view_tabs.length; i++) {
      if (g.view_tabs[i][0] != view_id) {
        continue;
      }

      // this takes care of Views that aren't arranged in tabs
      if (g.view_tabs[i][1] == 0) {
        g.view_tabs[i][1]++;
      }

      for (var j=1; j<=g.view_tabs[i][1]; j++) {
        var sel = "";
        if (j == selected_page) {
          sel = " class=\"selected\"";
        }
        html += "<li" + sel + ">" + j + "</li>";
      }
    }

    // review page
    var num_view_tabs = builder_js.get_num_view_tabs();
    var thanks_page_num = num_view_tabs+2;
    if ($("#irp").attr("checked")) {
      var review_page_num = num_view_tabs+1;
      thanks_page_num = num_view_tabs+2;
      html += "<li" + ((builder_js.current_page == review_page_num) ? " class=\"selected\"" : "") + ">" + g.messages["word_review"] + "</li>";
    }
    html += "<li" + ((builder_js.current_page == thanks_page_num) ? " class=\"selected\"" : "") + ">" + g.messages["word_thanks"] + "</li>";
  }
  html += "</ul><div class=\"hidden\" id=\"nav_pages_loaded\"></div>";

  $("#pages").html(html);
}


builder_js.update_window_size = function() {
  var window_width  = $(window).width();
  var window_height = $(window).height();
  var iframe_width = (builder_js.sidebar_visible) ? window_width - g.sidebar_width : window_width;

  $("#preview_iframe").css({
    width:  iframe_width - 1, // 1px for the left border
    height: window_height - (g.header_height + g.footer_height + g.iframe_header_height)
  });
  $("#iframe_header").css({ "width": iframe_width - 1});
  $("#sidebar").css({
    height: window_height - (g.header_height + g.footer_height)
  });
}


/**
 * Nifty little function to temporarily hide the sidebar to allow more room for examining the form.
 */
builder_js.init_sidebar_toggle = function() {
  $("#toggle_sidebar").bind("click", function() {
    if (builder_js.sidebar_animating) {
      return;
    }
    builder_js.sidebar_animating = true;
    var iframe         = $("#preview_iframe");
    var iframe_header  = $("#iframe_header");
    var original_width = parseInt(iframe.css("width"));

    // hide the sidebar!
    if (builder_js.sidebar_visible) {
      var original_left  = parseInt(iframe.css("left"));

      $("#sidebar").animate({
        opacity: 0,
        left:    "-=" + g.sidebar_width,
      }, {
        duration: 600,
        step: function(now, fx) {
          if (fx.prop != "left") {
            return;
          }
          iframe.css("left", original_left + now);
          iframe.css("width", original_width - now);
          iframe_header.css("left", original_left + now);
          iframe_header.css("width", original_width - now);
        },
        complete: function() {
          $("#toggle_sidebar").html(g.messages["phrase_show_sidebar"]);
          builder_js.sidebar_animating = false;
          builder_js.sidebar_visible = false;
        }
      });

    // show the toolbar again
    } else {
      $("#sidebar").animate({
        opacity: 1,
        left:    "+=" + g.sidebar_width
      }, {
        duration: 600,
        step: function(now, fx) {
          if (fx.prop != "left") {
            return;
          }
          iframe.css("left", g.sidebar_width + now);
          iframe.css("width", original_width - (g.sidebar_width + now));
          iframe_header.css("left", g.sidebar_width + now);
          iframe_header.css("width", original_width - (g.sidebar_width + now));
        },
        complete: function() {
          $("#toggle_sidebar").html(g.messages["phrase_hide_sidebar"]);
          builder_js.sidebar_animating = false;
          builder_js.sidebar_visible = true;
        }
      });
    }
  });
}


/**
 * This is used for editing the Thankyou page content and Form Offline page content in a full screen editor.
 */
builder_js.full_screen_editor = function(page) {
  var dialog    = null;
  var title     = null;
  var source_el = null;
  var target_id = null;
  if (page == "thankyou_page") {
    dialog    = $("#edit_thankyou_page_dialog");
    title     = g.messages["phrase_thankyou_page_content"];
    source_el = $("#thankyou_page_content");
    target_id = "thankyou_page_editor";
  } else {
    dialog    = $("#edit_form_offline_page_dialog");
    title     = g.messages["phrase_form_offline_page_content"];
    source_el = $("#form_offline_page_content");
    target_id = "form_offline_page_editor";
  }

  // always make the size of the the dialog relative to the main window
  var width  = $(window).width() - 100;
  var height = $(window).height() - 120;
  var editor = null;

  ft.create_dialog({
    dialog: dialog,
    title:  title,
    width:  width,
    height: height,
    open: function() {
      $(this).dialog( "option", "resizable", false);
      var width  = $(this).dialog("option", "width");
      var height = $(this).dialog("option", "height");
      $("#" + target_id).val(source_el.val());
      $(".dialog_page").css({
        width: width - 200,
        height: height - 103
      });
      editor = new CodeMirror.fromTextArea(target_id, {
        parserfile: ["parsexml.js"],
        path: g.root_url + "/global/codemirror/js/",
        stylesheet: g.root_url + "/global/codemirror/css/xmlcolors.css"
      });
    },
    buttons: [{
      text:  g.messages["word_cancel"],
      click: function() {
        $(this).dialog("close");
      }
    },
    {
      text:  g.messages["word_update"],
      click: function() {
        source_el.val(editor.getCode());
        $(this).dialog("close");
      }
    },
    {
      text:  g.messages["phrase_update_and_show"],
      click: function() {
      source_el.val(editor.getCode());
        $(this).dialog("close");
        if (page == "thankyou_page") {
          if (!$("#is_online").attr("checked")) {
            $("#is_online").attr("checked", "checked");
          } else {
            builder_js.update_page_links(builder_js.current_page);
          }
        } else {
          if ($("#is_online").attr("checked")) {
            $("#is_online").attr("checked", "");
            builder_js.update_page_links(builder_js.current_page);
          }
        }
        builder_js.submit_form();
      }
    }]
  });
}

builder_js.show_help_dialog = function() {
  var width  = $(window).width() - 100;
  var height = $(window).height() - 120;
  ft.create_dialog({
    dialog: $("#help_dialog"),
    title:  g.messages["word_help"],
    width:  width,
    height: height,
    open: function() {
      $(this).dialog("option", "resizable", false);
      var width  = $(this).dialog("option", "width");
      var height = $(this).dialog("option", "height");
      $(".dialog_page").css({
        width: width - 208,
        height: height - 111
      });
    },
    buttons: [{
      text:  g.messages["word_close"],
      click: function() {
        $(this).dialog("close");
      }
    }]
  });
}


builder_js.start_page_load = function() {
  $("#page_loading").show();
}


/**
 * Called once the page has been loaded. We override all form submits and links to prevent them
 * from working. This ensures that when the user clicks
 */
builder_js.page_loaded = function() {
  $("#page_loading").hide();
  var iframe_doc = $($("#preview_iframe")[0].contentWindow.document);
  iframe_doc.find("form").removeAttr("action").bind("submit", function() { return false; });
  iframe_doc.find("a").bind("click", function() { return false; });
}


/**
 * Called whenever the user selects a different template set. It updates the Placeholders and Templates
 * section to show the appropriate information. For speed, it saves + loads data already loaded in the page
 * via a cache.
 */
builder_js.select_template_set = function() {
  var set_id = $("#template_set_id").val();

  // store the current template set info
  builder_js.template_set_cache["set_" + builder_js.current_set_id] = {
    templates_html:   $("#template_settings").html(),
    placeholder_html: $("#placeholders").html()
  }

  // update the current set ID
  builder_js.current_set_id = set_id;

  if (typeof builder_js.template_set_cache["set_" + set_id] != "undefined") {
    $("#template_settings_heading, #placeholders_heading").removeClass("loading");
    $("#template_settings").html(builder_js.template_set_cache["set_" + set_id].templates_html);
    $("#placeholders").html(builder_js.template_set_cache["set_" + set_id].placeholder_html);
    builder_js.display_section_heading_count("templates");
    builder_js.display_section_heading_count("placeholders");
  } else {

    // display the swirlies for the templates and placeholders sections
    $("#template_settings_heading, #placeholders_heading").addClass("loading");
    $("#template_count, #placeholder_count").html(g.messages["word_loading_p"]);

    // templates
    $.ajax({
      url:  g.root_url + "/modules/form_builder/global/code/actions.php",
      type: "POST",
      data: {
        action: "get_template_set_templates_html",
        set_id: set_id
      },
      dataType: "html",
      success: function(html) {
        $("#template_settings_heading").removeClass("loading");
        $("#template_settings").html(html);

        ft.queue.push([
          function() { builder_js.display_section_heading_count("templates"); },
          function() { return ($("#templates_loaded").length > 0); }
        ]);
        ft.process_queue();
      }
    });

    // placeholders
    $.ajax({
      url:  g.root_url + "/modules/form_builder/global/code/actions.php",
      type: "POST",
      data: {
        action: "get_template_set_placeholders_html",
        set_id: set_id
      },
      dataType: "html",
      success: function(html) {
        $("#placeholders_heading").removeClass("loading");
        $("#placeholders").html(html);
        ft.queue.push([
          function() { builder_js.display_section_heading_count("placeholders"); },
          function() { return ($("#placeholders_loaded").length > 0); }
        ]);
        ft.process_queue();
      }
    });
  }
}


/**
 * Called on page load and whenever the user selected a new template set. It figures out how
 * many placeholders / templates there are and updates the count in the heading.
 */
builder_js.display_section_heading_count = function(section) {
  if (section == "templates") {
    var count = $("#template_settings select").length;
    $("#template_count").html("(" + count + ")");
  } else {
    var count = $(".pids").length;
    $("#placeholder_count").html("(" + count + ")");
  }
}


/**
 * Called on page load, this adds the appropriate event handlers for the dialog window nav + internal links.
 */
builder_js.init_dialogs = function() {
  $(".paged_dialogs").each(function() {
    var dialog_id = $(this).attr("id");

    // do the main nav links for the dialog
    $(this).find("ul.main_nav").bind("click", function(e) {
      if ($(e.target).hasClass("selected") || $(e.target).hasClass("rowN")) {
        return;
      }
      // now hide & show the appropriate page & nav item
      var selected_page = $(e.target).attr("class");
      builder_js.goto_page(dialog_id, selected_page);
    });

    // now assign the internal links
    $(this).find(".dialog_link").each(function() {
      var classes = $(this).attr("class").split(" ");
      var selected_page = classes.splice($.inArray("selected", classes), 1);
      $(this).bind("click", function() {
        builder_js.goto_page(dialog_id, selected_page);
      });
    });

  });
}


builder_js.goto_page = function(dialog_id, selected_page) {
  $("#" + dialog_id + " .main_nav").find(".selected").removeClass("selected");
  $("#" + dialog_id + " ." + selected_page).addClass("selected");
  $("#" + dialog_id).find(".dialog_page").hide();
  $("#" + dialog_id).find("." + selected_page + "_page").show();
}


/**
 * Called when the user clicks the "Save" button, and also when they attempt to publish a form
 * that hasn't been saved yet.
 */
builder_js.save_settings = function(callee) {
  // if this node exists, it means the user hasn't published the form yet.
  if ($("#publish_filename").length) {
    $("#filename").val($("#publish_filename").val());
    $("#folder_url").val($("#publish_folder_url").val());
    $("#folder_path").val($("#publish_folder_path").val());
  } else {
    $("#filename").val($("#new_publish_filename").val());
    $("#folder_url").val($("#new_publish_folder_url").val());
    $("#folder_path").val($("#new_publish_folder_path").val());
  }

  var form_data = $("#f").serialize();
  form_data = "action=save_builder_settings&" + form_data;
  $.ajax({
    url:  g.root_url + "/modules/form_builder/global/code/actions.php",
    type: "post",
    data: form_data,
    dataType: "json",
    success: function(response) {
      if (response.success == 1) {
        $("#published_form_id").val(response.published_form_id);
        if (callee == "publish") {
          builder_js.publish_form();
          return;
        }
        ft.create_dialog({
          title: g.messages["word_saved"],
          dialog: $("#form_saved_dialog"),
          content: "<div class=\"dialog_pad\">" + g.messages["notify_form_config_saved"] + "</div>",
          buttons: [{
            text:  g.messages["word_close"],
            click: function() {
              $(this).dialog("close");
            }
          }]
        })
      }
    }
  });
}


builder_js.show_publish_dialog = function() {
  var buttons = [{
    text:  g.messages["word_close"],
    click: function() {
      $(this).dialog("close");
    }
  }];
  if (!g.demo_mode) {
    buttons.push({
      text:  g.messages["word_publish"],
      click: builder_js.publish_form_check
    });
  }

  ft.create_dialog({
    dialog: $("#publish_form_dialog"),
    title:  g.messages["phrase_publish_form"],
    width:  650,
    open: function() {
      $(this).dialog("option", "resizable", false);
    },
    buttons: buttons
  });
}


/**
 * Opens the Publish Settings dialog.
 */
builder_js.show_publish_settings_dialog = function() {
  var buttons = [{
    text:  g.messages["word_close"],
    click: function() {
      $(this).dialog("close");
    }
  }];
  if (!g.demo_mode) {
    buttons.push({
      text:  g.messages["word_update"],
      click: builder_js.republish_form_check
    });
  }

  ft.create_dialog({
    dialog: $("#publish_settings_form_dialog"),
    title:  g.messages["phrase_publish_settings"],
    width:  650,
    open: function() {
      $(this).dialog("option", "resizable", false);
      $("#publish_settings_message").addClass("hidden");
      $("#publish_settings_display").show();
      $("#publish_settings_response").hide();
    },
    buttons: buttons
  });
}


builder_js.close_window = function(e) {
  window.opener.focus();

  // if the parent window is still on the publish tab, refresh the page to ensure it has the latest content
  if ($(window.opener.document).find("#publish_tab_identifier").length) {
    window.opener.location = g.root_url + "/admin/forms/edit.php?page=publish";
  }
  try {
    if (e.data.close) {
      window.close();
    }
  } catch(e) { }
}


/**
 * This is called when the user submits the Publish Form dialog. It checks that the form has first been
 * saved and that the published_form_id is available, then actually tries to publish the form with the
 * supplied valies.
 */
builder_js.publish_form_check = function() {
  var errors = [];

  var filename = $.trim($("#publish_filename").val());
  if (!filename) {
    errors.push(g.messages["validation_no_filename"]);
  } else if (filename.match(/\W/)) {
    errors.push(g.messages["validation_filename_not_alpha"])
  }
  var folder_url = $.trim($("#publish_folder_url").val());
  if (!folder_url) {
    errors.push(g.messages["validation_no_folder_url"]);
  }
  var folder_path = $.trim($("#publish_folder_path").val());
  if (!filename) {
    errors.push(g.messages["validation_no_folder_path"]);
  }

  if (errors.length) {
    var error_str = "";
    for (var i=0; i<errors.length; i++) {
      error_str += "&bull; " + errors[i] + "<br />";
    }
    $("#publish_message").removeClass("hidden");
    $("#publish_message_inner").find("div").html(error_str);
  } else {
    var published_form_id = $("#published_form_id").val();
    if (!published_form_id) {
      builder_js.save_settings("publish");
    } else {
      builder_js.publish_form();
    }
  }
}


builder_js.publish_form = function() {
  $.ajax({
    url:  g.root_url + "/modules/form_builder/global/code/actions.php",
    type: "POST",
    data: {
      action: "publish_form",
      published_form_id:   $("#published_form_id").val(),
      publish_filename:    $("#publish_filename").val(),
      publish_folder_url:  $("#publish_folder_url").val(),
      publish_folder_path: $("#publish_folder_path").val()
    },
    dataType: "json",
    success: function(json) {
      if (json.success == 1) {
        $("#publish_url").find("a").attr("href", json.url).html(json.url);
        $("#publish_url").removeClass("hidden");

        // update the Publish button to make it the "Publish Settings" button, with a different action
        $(".publish_btn").unbind("click").removeClass(".publish_btn")
          .addClass("publish_settings_btn").bind("click", builder_js.show_publish_settings_dialog)
          .html(g.messages["phrase_publish_settings"].toUpperCase());

        // now store all the publish field info into the Publish Settings dialog, in case they want to change something right now
        var filename = json.filename.replace(/\.php$/, "");
        $("#new_publish_filename").val(filename);
        $("#old_publish_filename").val(filename);
        $("#new_publish_folder_url").val(json.folder_url);
        $("#old_publish_folder_url").val(json.folder_url);
        $("#new_publish_folder_path").val(json.folder_path);
        $("#old_publish_folder_path").val(json.folder_path);

        // this overwrites the content of the publish form dialog - we don't need it any more
        $("#publish_form_dialog").html("<div class=\"margin_bottom\">" + g.messages["notify_form_published"] + "</div>"
          + "<input type=\"text\" class=\"large_textbox\" value=\"" + json.url + "\">"
          + "<a href=\"" + json.url + "\" target=\"_blank\" id=\"publish_dialog_open_form_link\">" + g.messages["phrase_open_form_in_new_window"] + "</a>"
        );

        $("#publish_form_dialog").dialog("option", "buttons", [
          {
            text: g.messages["word_close"],
            click: function() {
              $("#publish_form_dialog").dialog("close");
            }
          }
        ]);
      } else {
        $("#publish_message").removeClass("hidden");
        $("#publish_message_inner").find("div").html(json.message);
      }
    }
  });
}


/**
 * Called when updating the publish settings.
 */
builder_js.republish_form_check = function() {

  // check that at least something has changed (filename or folder)
  var old_filename = $.trim($("#old_publish_filename").val());
  var new_filename = $.trim($("#new_publish_filename").val());
  var old_folder_url = $.trim($("#old_publish_folder_url").val());
  var new_folder_url = $.trim($("#new_publish_folder_url").val());
  var old_folder_path = $.trim($("#old_publish_folder_path").val());
  var new_folder_path = $.trim($("#new_publish_folder_path").val());

  var errors = [];
  if (old_filename == new_filename && old_folder_url == new_folder_url && old_folder_path == new_folder_path) {
    errors.push("&bull; " + g.messages["validation_no_publish_setting_changes"]);
  }
  if (!new_filename) {
    errors.push("&bull; " + g.messages["validation_no_filename"]);
  } else if (new_filename.match(/\W/)) {
    errors.push("&bull; " + g.messages["validation_filename_not_alpha"])
  }
  if (!new_folder_url) {
    errors.push("&bull; " + g.messages["validation_no_folder_url"]);
  }
  if (!new_folder_path) {
    errors.push("&bull; " + g.messages["validation_no_folder_path"]);
  }

  if (errors.length) {
    var error_str = errors.join("<br />");
    $("#publish_settings_message").removeClass("hidden");
    $("#publish_settings_message_inner").find("div").html(error_str);
  } else {
    var published_form_id = $("#published_form_id").val();
    builder_js.update_publish_settings();
  }
}


builder_js.update_publish_settings = function(params) {
  var settings = $.extend({
    override: false
  }, params);

  $.ajax({
    url:  g.root_url + "/modules/form_builder/global/code/actions.php",
    type: "POST",
    data: {
      action: "update_publish_settings",
      published_form_id: $("#published_form_id").val(),
      new_publish_filename:    $("#new_publish_filename").val(),
      old_publish_filename:    $("#old_publish_filename").val(),
      new_publish_folder_url:  $("#new_publish_folder_url").val(),
      old_publish_folder_url:  $("#old_publish_folder_url").val(),
      new_publish_folder_path: $("#new_publish_folder_path").val(),
      old_publish_folder_path: $("#old_publish_folder_path").val(),
      override: settings.override
    },
    dataType: "json",
    success: function(json) {
      if (json.success == 1) {
        $("#publish_url").find("a").attr("href", json.url).html(json.url);
        $("#publish_dialog_open_form_link").attr("href", json.url);
        $("#publish_settings_response .large_textbox").val(json.url);
        $("#publish_settings_display").hide();
        $("#publish_settings_response").show();
        $("#old_publish_filename").val($("#new_publish_filename").val());
        $("#old_publish_folder_url").val($("#new_publish_folder_url").val());
        $("#old_publish_folder_path").val($("#new_publish_folder_path").val());

        $("#publish_settings_form_dialog").dialog("option", "buttons", [
          {
            text: g.messages["word_close"],
            click: function() {
              $("#publish_settings_form_dialog").dialog("close");
            }
          }
        ]);
      } else {
        $("#publish_settings_message").removeClass("hidden");
        $("#publish_settings_message_inner").find("div").html(json.message);
      }
    }
  });
}


/**
 * This is called when the user attempted to update the publish settings, but the old version of the published file couldn't
 * be found or deleted. In that instance, the user is notified, but given the option to ignore it and just publish the form
 * at the new location.
 */
builder_js.overide_publish_settings = function() {
  builder_js.update_publish_settings({ override: true });
}


builder_js.select_page = function(e) {
  var index = $(e.target).index();
  var page = index+1;
  builder_js.update_nav(page, true);
}


builder_js.update_nav = function(page, submit) {
  $("#page").val(page);
  builder_js.current_page = page;
  $("#pages").find("li").removeClass("selected");
  $("#pages ul li:nth-child(" + page + ")").addClass("selected");

  if (submit) {
    builder_js.submit_form();
  }
};


/**
 * This is a programmatic way to return the total number of visible pages in the form. It ignores the actual displayed
 * pages row and looks at what's selected for the View, Review Page checkbox, etc.
 */
builder_js.get_num_pages = function() {
  var num_tabs_in_view = builder_js.get_num_view_tabs();
  var num_pages = num_tabs_in_view+1;
  if ($("#irp").attr("checked")) {
    num_pages++;
  }
  return num_pages;
}


builder_js.get_num_view_tabs = function() {
  var curr_view_id = parseInt($("#view_id").val(), 10);
  var num_tabs_in_view = 1;
  for (var i=0; i<g.view_tabs.length; i++) {
    if (g.view_tabs[i][0] != curr_view_id) {
      continue;
    }
    num_tabs_in_view = g.view_tabs[i][1];
    break;
  }

  return num_tabs_in_view;
}

