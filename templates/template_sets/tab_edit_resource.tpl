<div class="subtitle underline margin_top_large">
    <a href="?page=resources">{$L.word_resources|upper}</a> &raquo; {$L.phrase_edit_resource|upper}
</div>

{ft_include file='messages.tpl'}

<form action="{$same_page}" method="post">
    <input type="hidden" name="resource_id" id="resource_id" value="{$resource_id}"/>

    <table cellspacing="2" cellpadding="1" class="margin_bottom_large">
        <tr>
            <td width="180">Resource Name</td>
            <td><input type="text" name="resource_name" style="width:580px" maxlength="255"
                       value="{$resource_info.resource_name|escape}"/></td>
        </tr>
        <tr>
            <td valign="top">{$L.word_placeholder}</td>
            <td>
                <input type="text" name="placeholder" style="width:580px" maxlength="255"
                       value="{$resource_info.placeholder|escape}"/>
                <div class="hint">{$text_resource_placeholder_hint}</div>
            </td>
        </tr>
        <tr>
            <td>{$L.phrase_resource_type}</td>
            <td>
                <input type="radio" name="resource_type" id="rt1" value="css"
                       {if $resource_info.resource_type == "css"}checked{/if} />
                <label for="rt1">CSS</label>
                <input type="radio" name="resource_type" id="rt2" value="js"
                       {if $resource_info.resource_type == "js"}checked{/if} />
                <label for="rt2">JavaScript</label>
            </td>
        </tr>
    </table>

    <div style="border: 1px solid #666666; padding: 3px; width: 687px;">
        <textarea id="resource_content" name="resource_content" style="height: 350px">{$resource_info.content|escape}</textarea>
    </div>

    <script>
      var codemirror_resource_content = new CodeMirror.fromTextArea(document.getElementById("resource_content"), {literal}{{/literal}
        mode: "css"
      {literal}});{/literal}
    </script>

    <p>
        <input type="submit" name="update" value="{$LANG.word_update}"/>
    </p>

</form>
