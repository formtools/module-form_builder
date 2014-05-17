
  <div class="underline margin_top_large">
    <div style="float:right; padding-right: 0px; margin-top: -4px;">{$previous_template_link} &nbsp; {$next_template_link}</div>
    <div class="subtitle">
      <a href="?page=templates">{$L.word_templates|upper}</a> &raquo; {$L.phrase_edit_template|upper}
    </div>
  </div>

  {include file='messages.tpl'}

  <form action="{$same_page}" method="post">
    <input type="hidden" name="template_id" value="{$template_info.template_id}" />

	  <table class="margin_bottom_large">
	  <tr>
	    <td width="130">{$L.phrase_template_name}</td>
	    <td>
	      <input type="text" name="template_name" style="width:300px" maxlength="255" value="{$template_info.template_name|escape}" />
	    </td>
	  </tr>
	  <tr>
	    <td>{$L.phrase_template_type}</td>
	    <td class="medium_grey">
	      {display_template_type type=$template_info.template_type}
	    </td>
	  </tr>
		{if $template_info.template_type == "code_block"}
		<tr>
		  <td>{$L.word_placeholder}</td>
		  <td class="medium_grey">
		    {literal}{{code_block template_id={/literal}{$template_info.template_id}{literal}}}{/literal}
		  </td>
		</tr>
		{/if}
	  </table>

    <div style="border: 1px solid #666666; padding: 3px">
      <textarea id="template_content" name="template_content" style="width: 686px; height: 350px">{$template_info.content|escape}</textarea>
    </div>
    <script>
      var html_editor = new CodeMirror.fromTextArea("template_content", {literal}{{/literal}
      parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js",
                   "../contrib/php/js/tokenizephp.js", "../contrib/php/js/parsephp.js", "../contrib/php/js/parsephphtmlmixed.js"],
      stylesheet: ["{$g_root_url}/global/codemirror/css/xmlcolors.css", "{$g_root_url}/global/codemirror/css/jscolors.css",
                   "{$g_root_url}/global/codemirror/css/csscolors.css", "{$g_root_url}/global/codemirror/contrib/php/css/phpcolors.css",
                   "{$g_root_url}/modules/form_builder/global/css/codemirror.css"],
      path:       "{$g_root_url}/global/codemirror/js/",
      electricChars: false
      {literal}});{/literal}
    </script>

    <div class="grey_box margin_bottom_large template_type_placeholders">
      <div><a href="#" id="toggle_placeholders_link">Hide / show available templates and placeholders</a></div>
      <div id="placeholders_section" style="display:none">

        <div class="placeholder_group margin_bottom margin_top">
          <div><b>{$L.word_templates}</b></div>

          <table cellspacing="0" cellpadding="1" width="100%">
		      {if $template_info.template_type == "page_layout"}
		        <tr>
		          <td valign="top" class="medium_grey" width="200">{literal}{{header}}{/literal}</td>
		          <td>
		            Required. This includes the selected <b>Header</b> template.
		          </td>
		        </tr>
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{footer}}{/literal}</td>
		          <td>
		            Required. This includes the selected <b>Footer</b> template.
		          </td>
		        </tr>
		        <tr>
		          <td class="rowN medium_grey" valign="top">{literal}{{page}}{/literal}</td>
		          <td class="rowN">
		            Required. This includes the appropriate page template (form page, offline form page, review page or
		            thankyou page), depending on what's appropriate.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "form_page"}
		        <tr>
		          <td valign="top" class="medium_grey" width="200">{literal}{{navigation}}{/literal}</td>
		          <td>
		            This includes the selected <b>Navigation</b> template.
		          </td>
		        </tr>
		        <tr>
		          <td class="medium_grey" valign="top">{literal}{{continue_block}}{/literal}</td>
		          <td>
		            This includes the selected <b>Continue Block</b> template.
		          </td>
		        </tr>
		        <tr>
		          <td class="rowN medium_grey" valign="top">{literal}{{error_message}}{/literal}</td>
		          <td class="rowN">
		            This includes the <b>Error Message</b> block, used to display any server-side errors
		            after the user submits the form.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "review_page" ||
		              $template_info.template_type == "thankyou_page"}
		        <tr>
		          <td valign="top" class="medium_grey" width="200">{literal}{{navigation}}{/literal}</td>
		          <td>
		            This includes the selected <b>Navigation</b> template.
		          </td>
		        </tr>
		        <tr>
		          <td class="rowN medium_grey" valign="top">{literal}{{continue_block}}{/literal}</td>
		          <td class="rowN">
		            This includes the selected <b>Continue Block</b> template.
		          </td>
		        </tr>
		      {else}
		        <tr>
		          <td class="rowN medium_grey">No required templates.</td>
		        </tr>
		      {/if}
		      </table>

        </div>

        <div class="placeholder_group margin_bottom margin_top">
		      <div><b>{$L.word_placeholders}</b></div>

	        <table cellspacing="0" cellpadding="1" width="100%">
		      {if $template_info.template_type == "header"}
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$required_resources}}{/literal}</td>
		          <td>
		            Required. This should be included in the &lt;head&gt;. It includes all the Core javascript
		            and CSS, plus anything defined for the field types.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "form_page" || $template_info.template_type == "review_page"}
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$grouped_fields}}{/literal}</td>
		          <td>
		            The fields that appear on the page, grouped as they are defined on the form's
		            Edit View -> Fields tab.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "form_offline_page"}
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$form_offline_page_content}}{/literal}</td>
		          <td>
		            Contains the custom HTML / text that should be displayed when the form is offline.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "thankyou_page"}
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$thankyou_page_content}}{/literal}</td>
		          <td>
		            Contains the custom HTML / text that should be displayed on the thankyou page.
		          </td>
		        </tr>
		      {elseif $template_info.template_type == "navigation"}
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$pages}}{/literal}</td>
		          <td>
		            Contains the list of pages to be included in the page navigation.
		          </td>
		        </tr>
		      {/if}

		      {* the rest are available for all templates *}

		        <tr>
		          <td width="200" valign="top" class="medium_grey">{literal}{{$form_name}}{/literal}</td>
		          <td>The name of the form.</td>
		        </tr>
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$form_id}}{/literal}</td>
		          <td>The form ID.</td>
		        </tr>
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$view_name}}{/literal}</td>
		          <td>The name of the View.</td>
		        </tr>
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$view_id}}{/literal}</td>
		          <td>The view ID.</td>
		        </tr>
		        <tr>
		          <td valign="top" class="medium_grey">{literal}{{$num_pages}}{/literal}</td>
		          <td>The number of pages in the form (includes review and thankyou pages)</td>
		        </tr>
		        <tr>
		          <td valign="top" class="rowN medium_grey">{literal}{{$num_form_pages}}{/literal}</td>
		          <td class="rowN">The number of form pages only (doesn't include review or thankyou pages)</td>
		        </tr>
		      </table>

        </div>

        <div class="placeholder_group margin_bottom margin_top">
          <div><b>{$L.word_resources}</b></div>

          <div class="medium_grey">
            {if $resources|@count == 0}
              {$L.phrase_no_resources_defined}
            {else}
              {foreach from=$resources item=resource_info}
                {if $resource_info.resource_type == "css"}
                  {literal}{{$R.{/literal}{$resource_info.placeholder}{literal}}}{/literal}<br />
                {else}
                  {literal}{{$R.{/literal}{$resource_info.placeholder}{literal}}}{/literal}<br />
                {/if}
              {/foreach}
            {/if}
          </div>
  	    </div>

        <div class="placeholder_group margin_bottom margin_top">
          <div><b>{$L.phrase_custom_placeholders}</b></div>
          {if $placeholders|@count == 0}
            <div class="medium_grey">{$L.phrase_no_placeholders_defined}</div>
          {else}
            {foreach from=$placeholders item=p}
              <div class="medium_grey">{literal}{{${/literal}P.{$p.placeholder}{literal}}}{/literal}</div>
            {/foreach}
          {/if}
        </div>
      </div>
    </div>

	  <p>
	    <input type="submit" name="update_template" value="Update Template" />
	  </p>

  </form>
