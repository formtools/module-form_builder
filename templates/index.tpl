{include file='modules_header.tpl'}

  <table cellpadding="0" cellspacing="0">
  <tr>
    <td width="45"><a href="index.php"><img src="images/icon_form_builder.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      {$L.module_name}
    </td>
  </tr>
  </table>

  {include file='messages.tpl'}

  <div class="margin_bottom_large">
    {$L.text_template_set_intro}
  </div>

  {if $module_settings.demo_mode == "on"}
    <div class="error margin_bottom_large">
      <div style="padding: 6px">
        {$L.notify_form_builder_demo_mode}
      </div>
    </div>
  {/if}

  <form action="{$same_page}" method="post">

  {if $template_sets|@count == 0}
    <div class="notify">
      <div style="padding: 6px">
        {$L.notify_no_template_sets_defined}
      </div>
    </div>
  {else}

    <div class="sortable template_sets" id="{$sortable_id}">
      <input type="hidden" class="sortable__custom_delete_handler" value="fb_ns.delete_template_set" />
      <ul class="header_row">
        <li class="col1">{$LANG.word_order}</li>
        <li class="col2">{$L.phrase_template_set}</li>
        <li class="col3">{$L.phrase_where_used}</li>
        <li class="col4">{$L.phrase_is_complete}</li>
        <li class="col5">{$L.word_templates}</li>
        <li class="col6">{$L.word_resources}</li>
        <li class="col7">{$L.word_placeholders}</li>
        <li class="col8 edit"></li>
        <li class="col9 colN del"></li>
      </ul>
      <div class="clear"></div>
      <ul class="rows check_areas">
      {foreach from=$template_sets item=template_set name=row}
        {assign var=i value=$smarty.foreach.row.iteration}
        {assign var=set_id value=$template_set.set_id}
        <li class="sortable_row">
          <div class="row_content">
            <div class="row_group{if $smarty.foreach.row.last} rowN{/if}">
              <input type="hidden" class="sr_order" value="{$set_id}" />
              <ul>
                <li class="col1 sort_col">{$i}</li>
                <li class="col2">
                  {$template_set.set_name}
                  {if $template_set.version}
                    <span class="medium_grey">({$template_set.version})</span>
                  {/if}
                </li>
                <li class="col3">{display_template_set_usage set_id=$set_id format="dropdown"}</li>
                <li class="col4">
				          {if $template_set.is_complete == "yes"}
				            <span class="template_set_marker template_set_complete">{$LANG.word_yes|upper}</span>
				          {else}
				            <span class="template_set_marker template_set_incomplete">{$LANG.word_no|upper}</span>
				          {/if}
                </li>
                <li class="col5 check_area"><a href="template_sets/index.php?page=templates&set_id={$set_id}">{$template_set.templates|@count}</a></li>
                <li class="col6 check_area"><a href="template_sets/index.php?page=resources&set_id={$set_id}">{$template_set.resources|@count}</a></li>
                <li class="col7 check_area"><a href="template_sets/index.php?page=placeholders&set_id={$set_id}">{$template_set.placeholders|@count}</a></li>
                <li class="col8 edit"><a href="template_sets/index.php?page=info&set_id={$set_id}"></a></li>
                <li class="col9 colN{if $template_set.usage|@count} info{else} del{/if}">
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
    {if $template_sets|@count > 1}
      <input type="submit" name="update_order" value="{$L.phrase_update_order}" />
    {/if}
    <input type="button" id="create_new_template_set" value="{$L.phrase_create_new_template_set}" />
  </p>

  </form>

  <div class="hidden" id="create_new_template_set_dialog">
    <div id="create_error" class="margin_bottom_large" style="display:none"></div>
	  <table style="width: 100%">
	    <tr>
	      <td width="180" class="medium_grey">{$L.phrase_template_set_name}</td>
	      <td><input type="text" id="new_template_name" style="width: 100%;" /></td>
	    </tr>
	    {if $template_sets|@count > 0}
	    <tr>
	      <td class="medium_grey">{$L.phrase_base_new_template_set_on}</td>
	      <td>
	        {template_sets name_id="original_set_id" only_return_complete=false is_base_on_dropdown=true}
	      </td>
	    </tr>
	    {/if}
	  </table>
  </div>


{include file='modules_footer.tpl'}
