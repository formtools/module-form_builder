  <div class="subtitle underline margin_top_large">
    <a href="?page=placeholders">{$L.word_placeholders|upper}</a> &raquo; {$L.phrase_add_placeholder|upper}
  </div>

  {include file='messages.tpl'}

  <form action="{$same_page}" method="post" onsubmit="return rsv.validate(this, rules)">
    <input type="hidden" name="num_rows" id="num_rows" value="0" />

    <table cellspacing="2" cellpadding="1" class="margin_bottom_large">
    <tr>
      <td width="150">{$L.phrase_placeholder_label}</td>
      <td><input type="text" name="placeholder_label" style="width:580px" maxlength="255" /></td>
    </tr>
    <tr>
      <td valign="top" width="150">{$L.word_placeholder}</td>
      <td>
        <input type="text" name="placeholder" style="width:580px" maxlength="255" />
        <div class="hint">{$L.text_placeholder_hint}</div>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_field_type}</td>
      <td>
        <select name="field_type" id="field_type">
          <option value="" selected>{$LANG.phrase_please_select}</option>
          <option value="textbox">{$LANG.word_textbox}</option>
          <option value="textarea">{$LANG.word_textarea}</option>
          <option value="radios">{$LANG.phrase_radio_buttons}</option>
          <option value="checkboxes">{$LANG.word_checkboxes}</option>
          <option value="select">{$LANG.word_dropdown}</option>
          <option value="multi-select">{$LANG.phrase_multi_select}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>{$L.phrase_default_value}</td>
      <td><input type="text" name="default_value" value="" style="width:580px" /></td>
    </tr>
    </table>

    <div id="field_options_div" style="display:none">
      <div class="margin_bottom_large subtitle underline">{$LANG.phrase_field_options|upper}</div>
      <table>
        <tr>
          <td width="140">{$L.word_orientation}</td>
          <td>
            <input type="radio" name="field_orientation" id="fo1" value="horizontal" checked />
              <label for="fo1">{$LANG.word_horizontal}</label>
            <input type="radio" name="field_orientation" id="fo2" value="vertical" />
              <label for="fo2">{$LANG.word_vertical}</label>
            <input type="radio" name="field_orientation" id="fo3" value="na" />
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
      <input type="submit" name="add_placeholder" value="{$L.phrase_add_placeholder}" />
    </p>

  </form>
