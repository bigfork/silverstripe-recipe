<{$Tag} class="form__field-group<% if $extraClass %> {$extraClass}<% end_if %> <% if $ColumnCount %>multicolumn<% end_if %>" id="{$HolderID}">
	<% if $Tag == 'fieldset' && $Legend %>
		<legend>{$Legend}</legend>
	<% end_if %>

	{$Field}
</{$Tag}>
