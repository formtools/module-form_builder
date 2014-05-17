  <div class="subtitle margin_top_large underline">{$L.word_resources|upper}</div>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">{$L.text_resources_tab_intro}</div>

  <form action="{$same_page}" method="post">
    <input type="hidden" name="set_id" id="set_id" value="{$set_id}" />

  {if $resources|@count == 0}
    <div class="notify yellow_bg" class="margin_bottom_large">
      <div style="padding:8px">
        {$L.notify_no_template_set_resources}
      </div>
    </div>
  {else}
    <div class="sortable resources_list margin_bottom_large" id="{$sortable_id}">
      <input type="hidden" class="sortable__custom_delete_handler" value="fb_ns.delete_resource" />
      <ul class="header_row">
        <li class="col1">{$LANG.word_order}</li>
        <li class="col2">{$L.phrase_resource_name}</li>
        <li class="col3">{$L.word_placeholder}</li>
        <li class="col4">{$L.phrase_resource_type}</li>
        <li class="col5 edit"></li>
        <li class="col6 colN del"></li>
      </ul>
      <div class="clear"></div>
      <ul class="rows">
      {foreach from=$resources item=info name=row}
        {assign var=resource_id value=$info.resource_id}
        <li class="sortable_row">
          <div class="row_content">
            <div class="row_group{if $smarty.foreach.row.last} rowN{/if}">
              <input type="hidden" class="sr_order" value="{$info.resource_id}" />
              <ul>
                <li class="col1 sort_col">{$smarty.foreach.row.iteration}</li>
                <li class="col2">{$info.resource_name}</li>
                <li class="col3 medium_grey">{literal}{{{/literal}$R.{$info.placeholder}{literal}}}{/literal}</li>
                <li class="col4">{$info.resource_type|upper}</li>
                <li class="col5 edit"><a href="?page=edit_resource&resource_id={$resource_id}"></a></li>
                <li class="col6 colN del"></li>
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

    <div class="margin_top_large">
      {if $resources|@count > 1}
        <input type="submit" name="update_order" value="{$LANG.phrase_update_order}" />
      {/if}
      <input type="button" id="add_resource" value="{$L.phrase_add_resource}"  />
    </div>
  </form>

  <div id="add_resource_dialog" class="hidden">
    <table width="100%">
    <tr>
      <td class="medium_grey" width="150">{$L.phrase_resource_name}</td>
      <td>
        <input type="text" id="resource_name" style="width:100%" />
      </td>
    </tr>
    <tr>
      <td valign="top" class="medium_grey">{$L.phrase_resource_placeholder}</td>
      <td>
        <input type="text" id="placeholder" style="width:100%" />
        <div class="hint">
          {$L.phrase_resource_placeholder_desc}
        </div>
      </td>
    </tr>
    <tr>
      <td class="medium_grey">Resource Type</td>
      <td>
        <input type="radio" name="resource_type" id="rt1" value="css" checked="checked" />
          <label for="rt1">CSS</label>
        <input type="radio" name="resource_type" id="rt2" value="js" />
          <label for="rt2">JavaScript</label>
      </td>
    </tr>
    </table>
  </div>

