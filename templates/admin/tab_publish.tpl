  <div class="underline margin_top_large subtitle">{$L.phrase_published_forms|upper}</div>

  {ft_include file="messages.tpl"}

  <div class="margin_bottom_large">
    {$L.text_publish_tab_intro}
  </div>

  {if $demo_mode == "on"}
    <div class="error margin_bottom_large">
      <div style="padding: 6px">
        {$L.notify_form_builder_demo_mode}
      </div>
    </div>
  {/if}

  {* used by the Form Builder to let it know whether or not the parent page is still on the Publish tab *}
  <input type="hidden" id="publish_tab_identifier" />

  {if $form_info.form_type != "form_builder"}

    <form action="{$same_page}" method="post">
      <div class="notify margin_bottom_large">
        <div style="padding:6px">
          <div class="margin_bottom">
            {$text_non_form_builder_form}
          </div>
          <input type="submit" name="set_as_form_builder" value="{$L.phrase_set_form_as_form_builder}" />
        </div>
      </div>
    </form>

  {else}

    {if $published_forms.results|@count == 0}

      <div>
        <input type="button" id="publish_new_form" value="{$L.phrase_publish_this_form}" />
      </div>

    {else}

      <form action="edit.php?page=publish" method="post">
		    <div class="sortable form_builder_form_list margin_bottom_large" id="form_builder_form_list">
	        <input type="hidden" class="sortable__custom_delete_handler" value="fb_ns.delete_form_configuration" />
		      <ul class="header_row">
		        <li class="col1">{$LANG.word_order}</li>
		        <li class="col2">{$LANG.word_view}</li>
		        <li class="col3">{$L.phrase_template_set}</li>
		        <li class="col4">{$L.word_published}</li>
		        <li class="col5">{$L.word_online}</li>
		        <li class="col6">{$L.phrase_form_location}</li>
		        <li class="col7 edit"></li>
		        <li class="col8 colN del"></li>
		      </ul>
		      <div class="clear"></div>
		      <ul class="rows">
		        {foreach from=$published_forms.results item=info name=row}
	          {assign var=i value=$smarty.foreach.row.iteration}
		        <li class="sortable_row">
		          <div class="row_content">
		            <div class="row_group rowN">
		              <input type="hidden" class="sr_order" value="{$info.published_form_id}" />
		              <input type="hidden" class="is_published" value="{$info.is_published}" />
		              <ul>
		                <li class="col1 sort_col">{$i}</li>
		                <li class="col2"><a href="?page=edit_view&view_id={$info.view_id}">{display_view_name view_id=$info.view_id}</a></li>
		                <li class="col3"><a href="../../modules/form_builder/template_sets/index.php?set_id={$info.set_id}">{display_template_set_name set_id=$info.set_id}</a></li>
		                <li class="col4">
		                  {if $info.is_published == "yes"}
		                    <span class="green">{$LANG.word_yes}</span>
		                  {else}
		                    <span class="red">{$LANG.word_no}</span>
		                  {/if}
		                </li>
		                <li class="col5">
		                  {if $info.is_published == "no"}
		                    <span class="light_grey">&#8212;</span>
		                  {elseif $info.is_online == "yes"}
		                    <span class="green">{$LANG.word_yes}</span>
		                    {if $info.offline_date != "0000-00-00 00:00:00"}
		                      {assign var=d value=$info.offline_date|replace:':':''}
		                      {assign var=d value=$d|replace:' ':''}
		                      {assign var=d value=$d|replace:'-':''}
		                      <span class="publish_tab_offline_date">{$d|date_format:"%b %e, %Y %l:%M:%S %p"}</span>
		                    {/if}
		                  {else}
		                    <span class="red">{$LANG.word_no}</span>
		                  {/if}
		                </li>
		                <li class="col6">
	                    {if $info.is_published == "no"}
		                    <div class="empty light_grey">&#8212;</span>
		                  {else}
                        <a title="{$LANG.phrase_open_form_in_dialog}" target="_blank" class="show_form" href="{$info.folder_url}/{$info.filename}"></a>
		                    <div class="published_form_url">{$info.filename}</div>
		                  {/if}
		                </li>
		                <li class="col7 edit"></li>
		                <li class="col8 colN del"></li>
		              </ul>
		              <div class="clear"></div>
		            </div>
		          </div>
		          <div class="clear"></div>
		        </li>
		        {/foreach}
		      </ul>
		    </div>
		    <div class="clear"></div>

	      <div>
	        {if $published_forms.results|@count > 1}
	          <input type="submit" name="update_order" value="{$LANG.phrase_update_order}" />
	        {/if}
	        <input type="button" id="publish_new_form" value="{$L.phrase_publish_at_new_location}" />
	      </div>

	      <div id="confirm_delete_form_configuration_not_published" style="display:none">{$L.confirm_delete_form_configuration_not_published}</div>

	      <div id="confirm_delete_form_configuration_published" style="display:none">
	        <span class="popup_icon popup_type_warning"></span>
          <div class="margin_bottom_large">{$L.confirm_delete_published_form}</div>
          <div>
            <input type="checkbox" checked="checked" id="delete_form_config" name="delete_form_config" />
              <label for="delete_form_config">{$L.confirm_delete_published_form_config}</label>
          </div>
	      </div>
	    </form>

    {/if}

  {/if}