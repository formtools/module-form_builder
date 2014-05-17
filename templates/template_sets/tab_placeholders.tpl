  <div class="subtitle margin_top_large underline">{$L.word_placeholders|upper}</div>

  {include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_placeholders_intro}
  </div>

  <form action="{$same_page}" method="post">

  {if $placeholders|@count == 0}

    <div class="notify yellow_bg" class="margin_bottom_large">
      <div style="padding:8px">
        {$L.notify_no_template_set_placeholders}
      </div>
    </div>

  {else}

    <div class="sortable placeholder_list" id="{$sortable_id}">
      <input type="hidden" class="sortable__custom_delete_handler" value="fb_ns.delete_placeholder" />
      <ul class="header_row">
        <li class="col1">{$LANG.word_order}</li>
        <li class="col2">{$L.phrase_placeholder_label}</li>
        <li class="col3">{$L.word_placeholder}</li>
        <li class="col4">{$LANG.phrase_field_type}</li>
        <li class="col5 edit"></li>
        <li class="col6 colN del"></li>
      </ul>
      <div class="clear"></div>
      <ul class="rows">
      {foreach from=$placeholders item=info name=row}
        {assign var=placeholder_id value=$info.placeholder_id}
        <li class="sortable_row">
          <div class="row_content">
            <div class="row_group{if $smarty.foreach.row.last} rowN{/if}">
              <input type="hidden" class="sr_order" value="{$info.placeholder_id}" />
              <ul>
				        <li class="col1 sort_col">{$info.field_order}</li>
				        <li class="col2">{$info.placeholder_label}</li>
				        <li class="col3 medium_grey">{literal}{{{/literal}$P.{$info.placeholder}{literal}}}{/literal}</li>
				        <li class="col4">{display_placeholder_field_type type=$info.field_type}</li>
				        <li class="col5 edit"><a href="?page=edit_placeholder&placeholder_id={$placeholder_id}"></a></li>
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

    <p>
      {if $placeholders|@count > 1}
        <input type="submit" name="update" value="{$LANG.word_update}" />
      {/if}
      <input type="button" value="{$L.phrase_add_placeholder}" onclick="window.location='?page=add_placeholder'" />
    </p>
  </form>
