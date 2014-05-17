  <div class="subtitle margin_top_large underline">{$L.word_templates|upper}</div>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.text_template_list_desc}
  </div>

  <form method="post" action="{$same_page}">

    {if $template_set_info.templates|@count == 0}
      <div class="notify">
        <div style="padding: 6px">
          {$L.notify_no_template_set_defined_click_button}
        </div>
      </div>
    {else}

      {if $missing_templates_str != ""}
        <div class="error margin_bottom_large">
          <div style="padding: 6px">
            {$L.phrase_templates_missing_str_c}
            <span class="medium_grey">{$missing_templates_str}</span>
          </div>
        </div>
      {/if}

      <div class="sortable template_list" id="{$sortable_id}">
        <input type="hidden" class="sortable__custom_delete_handler" value="fb_ns.delete_template" />
        <ul class="header_row">
          <li class="col1">{$LANG.word_order}</li>
          <li class="col2">{$L.phrase_template_name}</li>
          <li class="col3">{$L.phrase_template_type}</li>
          <li class="col4">{$L.phrase_where_used}</li>
          <li class="col5 edit"></li>
          <li class="col6 colN del"></li>
        </ul>
        <div class="clear"></div>
        <ul class="rows">
        {foreach from=$template_set_info.templates item=template name=row}
          {assign var=i value=$smarty.foreach.row.iteration}
          {assign var=template_id value=$template.template_id}
          <li class="sortable_row">
            <div class="row_content">
              <div class="row_group{if $smarty.foreach.row.last} rowN{/if}">
                <input type="hidden" class="sr_order" value="{$template_id}" />
                <ul>
                  <li class="col1 sort_col">{$i}</li>
                  <li class="col2">{$template.template_name}</li>
                  <li class="col3">{display_template_type type=$template.template_type}</li>
                  <li class="col4">
                    {if $template.template_type == "code_block"}
                      <span class="pad_left light_grey">&#8212;</span>
                    {else}
                      {display_template_usage usage=$template.usage}
                    {/if}
                  </li>
                  <li class="col5 edit"><a href="index.php?page=edit_template&template_id={$template_id}"></a></li>
                  <li class="col6 colN{if $template.usage|@count > 0} info{else} del{/if}"></li>
                </ul>
              </div>
            </div>
            <div class="clear"></div>
          </li>
        {/foreach}
        </ul>
      </div>

      <div class="clear"></div>

    {/if}

    <p>
      <input type="hidden" id="set_id" value="{$template_set_info.set_id}" />
      {if $template_set_info.templates|@count > 1}
        <input type="submit" name="update_order" value="{$LANG.phrase_update_order}" />
      {/if}
      <input type="button" id="create_new_template" value="{$L.phrase_create_new_template}" />
    </p>

  </form>

  <div class="hidden" id="create_new_template_dialog">
    <input type="hidden" id="has_templates" value="{if $template_set_info.templates|@count == 0}no{else}yes{/if}" />
    <div id="create_error" class="margin_bottom_large" style="display:none"></div>
    <table style="width: 100%">
      <tr>
        <td width="200" class="medium_grey">{$L.phrase_template_name}</td>
        <td><input type="text" id="new_template_name" style="width: 100%;" /></td>
      </tr>
      {if $template_set_info.templates|@count > 0}
      <tr>
        <td class="medium_grey">
          <input type="radio" name="new_template_source" id="nts1" value="existing_template" checked="checked" />
            <label for="nts1">{$L.phrase_base_on_existing_template_c}</label>
        </td>
        <td>
          <select name="source_template_id" id="source_template_id">
            <option value="">{$LANG.phrase_please_select}</option>
            {foreach from=$template_set_info.templates item=t}
              <option value="{$t.template_id}">{$t.template_name}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td class="medium_grey">
          <input type="radio" name="new_template_source" id="nts2" value="new_template">
            <label for="nts2">{$L.phrase_new_template_select_type_c}</label>
        </td>
        <td>{template_types name_id="new_template_type" class="has_templates_new_template_dropdown"}</td>
      </tr>
      {else}
      <tr>
        <td class="medium_grey">{$L.phrase_template_type}</td>
        <td>{template_types name_id="new_template_type"}</td>
      </tr>
      {/if}
    </table>
  </div>

