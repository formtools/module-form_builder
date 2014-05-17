  <div class="subtitle margin_top_large underline">{$L.phrase_template_set_info|upper}</div>

  {include file="messages.tpl"}

  <form action="{$same_page}" method="post">
    <input type="hidden" name="set_id" id="set_id" value="{$set_id}" />

	  <table class="list_table margin_bottom_large">
		<tr>
		  <td width="140" class="pad_left_small medium_grey" valign="top">{$L.phrase_is_complete}</td>
		  <td>
		    {if $template_set_info.is_complete == "yes"}
		      <span class="template_set_marker template_set_complete">{$LANG.word_yes|upper}</span>
		    {else}
		      <span class="template_set_marker template_set_incomplete">{$LANG.word_no|upper}</span> {$L.phrase_missing_templates_c}
		      <span class="medium_grey">{$missing_templates_str}</span>
		    {/if}
		  </td>
		</tr>
		<tr>
		  <td class="pad_left_small medium_grey">{$L.phrase_template_set_name}</td>
		  <td>
		    <input type="text" style="width: 200px" name="set_name" value="{$template_set_info.set_name|escape}" maxlength="255" />
		  </td>
		</tr>
		<tr>
		  <td class="pad_left_small medium_grey">{$LANG.word_version}</td>
		  <td>
		    <input type="text" name="version" size="5" maxlength="20" value="{$template_set_info.version|escape}" />
		  </td>
		</tr>
		<tr>
		  <td class="pad_left_small medium_grey">{$L.word_description}</td>
		  <td>
        <textarea name="description" style="width:99%; height: 100px">{$template_set_info.description|escape}</textarea>
		  </td>
		</tr>
	  </table>

	  <div>
	    <input type="submit" name="update" value="{$LANG.word_update}" />
    </div>
  </form>

  <br />

  <div class="subtitle margin_bottom underline">{$L.word_usage|upper}</div>

  {if $usage|@count == 0}
    <span class="medium_grey">{$L.text_template_set_not_used}</span>
  {else}
    <div class="margin_bottom_large">
      {$L.text_template_set_usage_intro}
    </div>
    <table class="list_table margin_bottom_large">
    <tr>
      <th>{$LANG.word_form}</th>
      <th>{$LANG.word_view}</th>
      <th>{$L.phrase_form_location}</th>
    </tr>
    {foreach from=$usage key=form_id item=data}
      {foreach from=$data.usage item=i}
      <tr>
        <td class="pad_left_small"><a href="../../../admin/forms/edit.php?form_id={$form_id}&page=publish">{$data.form_name}</a></td>
        <td class="pad_left_small"><a href="../../../admin/forms/edit.php?form_id={$i.view_name}&page=publish">{$i.view_name}</a></td>
        <td class="pad_left_small">
          <a title="{$LANG.phrase_open_form_in_dialog}" target="_blank" class="show_form" href="{$i.full_url}"></a>
          <div class="published_form_url">{$i.filename}</div>
        </td>
      </tr>
      {/foreach}
    {/foreach}
    </table>
  {/if}
