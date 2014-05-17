{include file='modules_header.tpl'}

  <div style="float:right">
    {if $template_set_info.is_complete == "yes"}
		  <span class="template_set_marker template_set_complete">{$LANG.word_complete|upper}</span>
    {else}
      <span class="template_set_marker template_set_incomplete">{$LANG.word_incomplete|upper}</span>
    {/if}
  </div>

  <table cellpadding="0" cellspacing="0" class="margin_bottom_large">
  <tr>
    <td width="45"><a href="../"><img src="../images/icon_form_builder.png" border="0" width="34" height="34" /></a></td>
    <td class="title">
      <a href="../../../admin/modules">{$LANG.word_modules}</a>
      <span class="joiner">&raquo;</span>
      <a href="../">{$L.module_name}</a>
      <span class="joiner">&raquo;</span>
      {$template_set_info.set_name}
    </td>
  </tr>
  </table>

  {ft_include file='tabset_open.tpl'}

    {if $page == "info"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_info.tpl'}
    {elseif $page == "templates"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_templates.tpl'}
    {elseif $page == "edit_template"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_edit_template.tpl'}
    {elseif $page == "resources"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_resources.tpl'}
    {elseif $page == "edit_resource"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_edit_resource.tpl'}
    {elseif $page == "placeholders"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_placeholders.tpl'}
    {elseif $page == "add_placeholder"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_add_placeholder.tpl'}
    {elseif $page == "edit_placeholder"}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_edit_placeholder.tpl'}
    {else}
      {ft_include file='../../modules/form_builder/templates/template_sets/tab_info.tpl'}
    {/if}

  {ft_include file='tabset_close.tpl'}

{include file='modules_footer.tpl'}