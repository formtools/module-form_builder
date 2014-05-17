  <div class="subtitle margin_top_large underline">{$L.phrase_thankyou_page|upper}</div>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_default_thankyou_page_desc}
  </div>

  <form method="post" action="{$same_page}">
    <div class="editor">
      <textarea name="default_thankyou_page_content" id="default_thankyou_page_content" style="width:100%; height: 400px;">{$module_settings.default_thankyou_page_content|escape}</textarea>
    </div>
    <script>
    {literal}
    var editor = new CodeMirror.fromTextArea("default_thankyou_page_content", {
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
