{ft_include file='modules_header.tpl'}

<table cellpadding="0" cellspacing="0" class="margin_bottom_large">
    <tr>
        <td width="45"><a href="./"><img src="images/icon_form_builder.png" border="0" width="34" height="34"/></a></td>
        <td class="title">
            <a href="../../admin/modules">{$LANG.word_modules}</a>
            <span class="joiner">&raquo;</span>
            <a href="./">{$L.module_name}</a>
            <span class="joiner">&raquo;</span>
            {$LANG.word_settings}
        </td>
    </tr>
</table>

{ft_include file='tabset_open.tpl'}

{if $page == "main"}
    {include file='./tab_settings_main.tpl'}
{elseif $page == "thanks"}
    {include file='./tab_settings_thanks.tpl'}
{elseif $page == "form_offline"}
    {include file='./tab_settings_form_offline.tpl'}
{else}
    {include file='./tab_settings_main.tpl'}
{/if}

{ft_include file='tabset_close.tpl'}

{ft_include file='modules_footer.tpl'}
