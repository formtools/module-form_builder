{*
  This is called during the initial Form Builder page load, and any time the user changes the Template Set choice.
  It generates some HTML containing the list of templates available for the set. If there's only one, it hides the
  choice, but includes a hidden field containing the .
*}

  {if $grouped_templates.page_layout|@count > 1}
  <div>
    <label for="page_layout_template_id">{$L.phrase_page_layout}</label>
    <div>
      <select name="page_layout_template_id" id="page_layout_template_id" class="full">
      {foreach from=$grouped_templates.page_layout item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.page_layout == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="page_layout_template_id" value="{$grouped_templates.page_layout.0.template_id}" />
  {/if}

  {if $grouped_templates.header|@count > 1}
  <div>
    <label for="header_template_id">{$L.word_header}</label>
    <div>
      <select name="header_template_id" id="header_template_id" class="full">
      {foreach from=$grouped_templates.header item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.header == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="header_template_id" value="{$grouped_templates.header.0.template_id}" />
  {/if}

  {if $grouped_templates.footer|@count > 1}
  <div>
    <label for="footer_template_id">{$L.word_footer}</label>
    <div>
      <select name="footer_template_id" id="footer_template_id" class="full">
      {foreach from=$grouped_templates.footer item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.footer == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="footer_template_id" value="{$grouped_templates.footer.0.template_id}" />
  {/if}

  {if $grouped_templates.form_page|@count > 1}
  <div>
    <label for="form_page_template_id">{$L.phrase_form_page}</label>
    <div>
      <select name="form_page_template_id" id="form_page_template_id" class="full">
      {foreach from=$grouped_templates.form_page item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.form_page == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="form_page_template_id" value="{$grouped_templates.form_page.0.template_id}" />
  {/if}

  {if $grouped_templates.review_page|@count > 1}
  <div>
    <label for="review_page_template_id">{$L.phrase_review_page}</label>
    <div>
      <select name="review_page_template_id" id="review_page_template_id" class="full">
      {foreach from=$grouped_templates.review_page item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.review_page == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="review_page_template_id" value="{$grouped_templates.review_page.0.template_id}" />
  {/if}

  {if $grouped_templates.thankyou_page|@count > 1}
  <div>
    <label for="review_page_template_id">{$L.phrase_thankyou_page}</label>
    <div>
      <select name="thankyou_page_template_id" id="thankyou_page_template_id" class="full">
      {foreach from=$grouped_templates.thankyou_page item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.thankyou_page == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="thankyou_page_template_id" value="{$grouped_templates.thankyou_page.0.template_id}" />
  {/if}

  {if $grouped_templates.form_offline_page|@count > 1}
  <div>
    <label for="form_offline_page_template_id">{$L.phrase_form_offline_page}</label>
    <div>
      <select name="form_offline_page_template_id" id="form_offline_page_template_id" class="full">
      {foreach from=$grouped_templates.form_offline_page item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.form_offline_page == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="form_offline_page_template_id" value="{$grouped_templates.form_offline_page.0.template_id}" />
  {/if}

  {if $grouped_templates.navigation|@count > 1}
  <div>
    <label for="navigation_template_id">{$L.word_navigation}</label>
    <div>
      <select name="navigation_template_id" id="navigation_template_id" class="full">
      {foreach from=$grouped_templates.navigation item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.navigation == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="navigation_template_id" value="{$grouped_templates.navigation.0.template_id}" />
  {/if}

  {if $grouped_templates.continue_block|@count > 1}
  <div>
    <label for="continue_block_template_id">{$L.phrase_continue_block}</label>
    <div>
      <select name="continue_block_template_id" id="continue_block_template_id" class="full">
      {foreach from=$grouped_templates.continue_block item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.continue_block == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="continue_block_template_id" value="{$grouped_templates.continue_block.0.template_id}" />
  {/if}

  {if $grouped_templates.error_message|@count > 1}
  <div>
    <label for="error_message_template_id">{$L.phrase_error_message}</label>
    <div>
      <select name="error_message_template_id" id="error_message_template_id" class="full">
      {foreach from=$grouped_templates.error_message item=template name=row}
        <option value="{$template.template_id}" {if $selected_templates.error_message == $template.template_id}selected="selected"{/if}>{$template.template_name}</option>
      {/foreach}
      </select>
    </div>
  </div>
  {else}
    <input type="hidden" name="error_message_template_id" value="{$grouped_templates.error_message.0.template_id}" />
  {/if}

  <input type="hidden" id="templates_loaded" />