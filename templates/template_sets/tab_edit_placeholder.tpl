  <div class="subtitle underline margin_top_large">
    <a href="?page=placeholders">{$L.word_placeholders|upper}</a> &raquo; {$L.phrase_edit_placeholder|upper}
  </div>

  {include file='messages.tpl'}

  <form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" name="placeholder_id" id="placeholder_id" value="{$placeholder_id}" />

    <table cellspacing="2" cellpadding="1" class="margin_bottom_large">
    <tr>
      <td width="180">{$L.phrase_placeholder_label}</td>
      <td><input type="text" name="placeholder_label" style="width:550px" maxlength="255" value="{$placeholder_info.placeholder_label|escape}" /></td>
    </tr>
    <tr>
      <td valign="top">{$L.word_placeholder}</td>
      <td>
        <input type="text" name="placeholder" style="width:550px" maxlength="255" value="{$placeholder_info.placeholder|escape}" />
        <div class="hint">{$text_placeholder_hint}</div>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_field_type}</td>
      <td>
        <select name="field_type" id="field_type">
          <option value="" {if $placeholder_info.field_type == ""}selected="selected"{/if}>{$LANG.phrase_please_select}</option>
          <option value="textbox" {if $placeholder_info.field_type == "textbox"}selected="selected"{/if}>{$LANG.word_textbox}</option>
          <option value="textarea" {if $placeholder_info.field_type == "textarea"}selected="selected"{/if}>{$LANG.word_textarea}</option>
          <option value="radios" {if $placeholder_info.field_type == "radios"}selected="selected"{/if}>{$LANG.phrase_radio_buttons}</option>
          <option value="checkboxes" {if $placeholder_info.field_type == "checkboxes"}selected="selected"{/if}>{$LANG.word_checkboxes}</option>
          <option value="select" {if $placeholder_info.field_type == "select"}selected="selected"{/if}>{$LANG.word_dropdown}</option>
          <option value="multi-select" {if $placeholder_info.field_type == "multi-select"}selected="selected"{/if}>{$LANG.phrase_multi_select}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_default_value}</td>
      <td><input type="text" name="default_value" value="{$placeholder_info.default_value|escape}" style="width:550px" /></td>
    </tr>
    </table>

    <div id="field_options_div" {if $placeholder_info.field_type == "textbox" || $placeholder_info.field_type == "textarea" || $placeholder_info.field_type == "password"}style="display:none"{/if}>
      <div class="margin_bottom_large subtitle underline">{$LANG.phrase_field_options|upper}</div>
      <table>
        <tr>
          <td width="140">{$L.word_orientation}</td>
          <td>
            <input type="radio" name="field_orientation" id="fo1" value="horizontal"
              {if $placeholder_info.field_orientation == "horizontal"}checked="checked"{/if}
              {if $placeholder_info.field_type == "select" || $placeholder_info.field_type == "multi-select"}disabled="disabled"{/if} />
              <label for="fo1">{$LANG.word_horizontal}</label>
            <input type="radio" name="field_orientation" id="fo2" value="vertical"
              {if $placeholder_info.field_orientation == "vertical"}checked="checked"{/if}
              {if $placeholder_info.field_type == "select" || $placeholder_info.field_type == "multi-select"}disabled="disabled"{/if} />
              <label for="fo2">{$LANG.word_vertical}</label>
            <input type="radio" name="field_orientation" id="fo3" value="na"
              {if $placeholder_info.field_orientation == "na"}checked="checked"{/if}
              {if $placeholder_info.field_type == "checkboxes" || $placeholder_info.field_type == "radios"}disabled="disabled"{/if} />
              <label for="fo3">{$LANG.word_na}</label>
          </td>
        </tr>
        <tr>
          <td valign="top">{$LANG.word_options}</td>
          <td>
            <div class="sortable placeholder_option_list" id="{$sortable_id}">
              <ul class="header_row">
                <li class="col1">{$LANG.word_order}</li>
                <li class="col2">{$LANG.word_option}</li>
                <li class="col3 colN del"></li>
              </ul>
              <div class="clear"></div>
              <ul class="rows">
                {foreach from=$placeholder_info.options item=option_info}
                <li class="sortable_row">
                  <div class="row_content">
                    <div class="row_group">
                      <ul>
                        <li class="col1 sort_col">{$option_info.field_order}</li>
                        <li class="col2">
                          <input type="text" name="placeholder_options[]" value="{$option_info.option_text|escape}" />
                        </li>
                        <li class="col3 colN del"></li>
                      </ul>
                    </div>
                  </div>
                  <div class="clear"></div>
                </li>
                {/foreach}
              </ul>
            </div>
            <div class="clear"></div>
            <div>
              <a href="#" onclick="return fb_ns.add_placeholder_row()">{$LANG.phrase_add_row}</a>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <p>
      <input type="submit" name="update" value="{$LANG.word_update}" />
    </p>

  </form>
