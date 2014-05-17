  <div class="subtitle margin_top_large underline">{$L.phrase_form_offline_settings|upper}</div>

  {include file="messages.tpl"}

  <form method="post" action="{$same_page}">

    <table cellspacing="0" cellpadding="0" class="list_table margin_bottom_large">
    <tr>
      <td class="pad_left_small" width="170">{$L.phrase_offline_form_behaviour}</td>
      <td>
        <input type="radio" name="scheduled_offline_form_behaviour" id="sofb1" value="allow_completion"
          {if $module_settings.scheduled_offline_form_behaviour == "allow_completion"}checked="checked"{/if} />
          <label for="sofb1">{$L.phrase_allow_form_submission_completion}</label><br />
        <input type="radio" name="scheduled_offline_form_behaviour" id="sofb2" value="cutoff"
          {if $module_settings.scheduled_offline_form_behaviour == "cutoff"}checked="checked"{/if} />
          <label for="sofb2">{$L.phrase_immediately_prevent_all_submissions}</label>

        <div class="medium_grey pad_left_small">
          {$L.text_scheduled_offline_form_desc}
        </div>
      </td>
    </tr>
    </table>

    <div class="subtitle margin_bottom">{$L.phrase_default_offline_page_content}</div>

    <div class="editor">
      <textarea name="default_form_offline_page_content" id="default_form_offline_page_content"
        style="width:100%; height: 400px;">{$module_settings.default_form_offline_page_content|escape}</textarea>
    </div>
    <script>
    {literal}
    var editor = new CodeMirror.fromTextArea("default_form_offline_page_content", {
      parserfile: ["parsexml.js"],
      path: g.root_url + "/global/codemirror/js/",
      stylesheet: g.root_url + "/global/codemirror/css/xmlcolors.css"
    });
    {/literal}
    </script>
    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>
  </form>