  {if $placeholders|@count == 0}
    <div class="medium_grey">{$L.notify_no_placeholders}</div>
  {/if}

	{foreach from=$placeholders item=info}
    {assign var=pid value=$info.placeholder_id}
	  <input type="hidden" name="placeholder_ids[]" class="pids" value="{$pid}" />

    <div>
	    <label>{$info.placeholder_label}</label>
      <div>
        {if $info.field_type == "textbox"}
          <input type="text" name="placeholder_{$pid}" value="{$placeholder_hash.$pid|escape}" class="full" />
        {elseif $info.field_type == "textarea"}
          <textarea name="placeholder_{$pid}" style="width:98%; height: 60px">{$placeholder_hash.$pid}</textarea>
        {elseif $info.field_type == "password"}
          <input type="password" name="placeholder_{$pid}" value="{$placeholder_hash.$pid|escape}" size="20" />
        {elseif $info.field_type == "radios"}

        {foreach from=$info.options key=k2 item=option name=row}
          {assign var="count" value=$smarty.foreach.row.iteration}
          <input type="radio" name="placeholder_{$pid}" id="placeholder_{$pid}_{$count}" value="{$option.option_text|escape}"
            {if $option.option_text == $placeholder_hash.$pid}checked{/if} />
            <label for="placeholder_{$pid}_{$count}">{$option.option_text|escape}</label>
            {if $info.field_orientation == "vertical"}<br />{/if}
        {/foreach}

        {elseif $info.field_type == "checkboxes"}

          {assign var=selected_els value="|"|explode:$placeholder_hash.$pid}
            {foreach from=$info.options key=k2 item=option name="row"}
              {assign var="count" value=$smarty.foreach.row.iteration}
              <input type="checkbox" name="placeholder_{$pid}[]" id="placeholder_{$pid}_{$count}" value="{$option.option_text|escape}"
                {if $option.option_text|in_array:$selected_els}checked="checked"{/if} />
                <label for="placeholder_{$pid}_{$count}">{$option.option_text|escape}</label>
              {if $info.field_orientation == "vertical"}<br />{/if}
            {/foreach}

        {elseif $info.field_type == "select"}

          <select name="placeholder_{$pid}" class="full">
            {foreach from=$info.options key=k2 item=option}
              {assign var="escaped_value" value=$option.option_text|escape}
              <option value="{$option.option_text|escape}" {if $escaped_value == $placeholder_hash.$pid}selected{/if}>{$option.option_text}</option>
            {/foreach}
          </select>
        {elseif $info.field_type == "multi-select"}
          {assign var=selected_els value="|"|explode:$placeholder_hash.$pid}
          <select name="placeholder_{$pid}[]" multiple size="4" class="full">
            {foreach from=$info.options key=k2 item=option}
              {assign var="escaped_value" value=$option.option_text|escape}
              <option value="{$option.option_text|escape}"
              {if $option.option_text|in_array:$selected_els}selected="selected"{/if}>{$option.option_text}</option>
            {/foreach}
          </select>
        {/if}
      </div>
    </div>
  {/foreach}

  <input type="hidden" id="placeholders_loaded" />
