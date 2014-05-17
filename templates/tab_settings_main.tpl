  <div class="subtitle margin_top_large underline">{$L.phrase_default_publish_folder|upper}</div>

  {include file="messages.tpl"}

  <form method="post" action="{$same_page}">
    <table cellspacing="0" cellpadding="1" class="list_table margin_bottom_large">
    <tr>
      <td width="200" class="pad_left_small">{$L.phrase_default_form_folder}</td>
      <td>
        <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td><input type="text" name="default_published_folder_path" id="default_published_folder_path" style="width: 99%" value="{$module_settings.default_published_folder_path|escape}" /></td>
          <td width="170">
            <input type="button" value="{$LANG.phrase_test_folder_permissions}"
              onclick="ft.test_folder_permissions($('#default_published_folder_path').val(), 'permissions_result')" style="width: 170px;" />
          </td>
        </tr>
        </table>
        <div class="pad_left_small medium_grey">{$L.text_default_form_folder_desc}</div>
        <div id="permissions_result"></div>
      </td>
    </tr>
    <tr>
      <td class="pad_left_small">{$L.phrase_default_form_url}</td>
      <td>
        <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td><input type="text" name="default_published_folder_url" id="default_published_folder_url" style="width: 99%" value="{$module_settings.default_published_folder_url|escape}" /></td>
          {if $allow_url_fopen}
            <td width="170"><input type="button" value="{$LANG.phrase_confirm_folder_url_match}"
              onclick="ft.test_folder_url_match($('#default_published_folder_path').val(), $('#default_published_folder_url').val(), 'folder_match_message_id')" style="width: 170px;" /></td>
          {/if}
        </tr>
        </table>
        <div id="folder_match_message_id"></div>
        <div class="pad_left_small medium_grey">{$L.text_default_form_url_desc}</div>
      </td>
    </tr>
    </table>


    <div class="subtitle margin_bottom_large underline">{$L.phrase_labels_other|upper}</div>

    <table cellspacing="0" cellpadding="1" class="list_table">
    <tr>
      <td width="200" class="pad_left_small">{$L.phrase_default_review_page_title}</td>
      <td>
        <input type="text" name="review_page_title" id="review_page_title" style="width: 99%" value="{$module_settings.review_page_title|escape}" />
        <div class="pad_left_small medium_grey">{$L.text_default_review_page_title_desc}</div>
      </td>
    </tr>
    <tr>
      <td class="pad_left_small">{$L.phrase_default_thankyou_nav_title}</td>
      <td>
        <input type="text" name="thankyou_page_title" id="thankyou_page_title" style="width: 99%" value="{$module_settings.thankyou_page_title|escape}" />
        <div class="pad_left_small medium_grey">{$L.text_default_thankyou_page_title_desc}</div>
      </td>
    </tr>
    <tr>
      <td class="pad_left_small">{$L.phrase_default_visual_editor_window_size}</td>
      <td class="pad_left_small">
        <table cellspacing="0" cellpadding="0">
        <tr>
          <td width="60" class="medium_grey"><label for="width">{$L.word_width}</label></td>
          <td><input type="text" name="form_builder_width" id="width" size="4" value="{$module_settings.form_builder_width}" />px</td>
        </tr>
        <tr>
          <td class="medium_grey"><label for="height">{$L.word_height}</label></td>
          <td><input type="text" name="form_builder_height" id="height" size="4" value="{$module_settings.form_builder_height}" />px</td>
        </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="pad_left_small">{$L.phrase_edit_in_form_builder_link_action}</td>
      <td>
        <input type="radio" name="edit_form_builder_link_action" id="efba1" value="new_window"
          {if $module_settings.edit_form_builder_link_action == "new_window"}checked="checked"{/if} />
          <label for="efba1">{$L.phrase_new_window}</label>
        <input type="radio" name="edit_form_builder_link_action" id="efba2" value="same_window"
          {if $module_settings.edit_form_builder_link_action == "same_window"}checked="checked"{/if} />
          <label for="efba2">{$L.phrase_same_window}</label>
        <div class="pad_left_small medium_grey">{$L.text_edit_form_builder_link_action_desc}</div>
      </td>
    </tr>
    </table>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>
  </form>