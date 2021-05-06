<% if $IncludeFormTag %>
	<form {$AttributesHTML}>
<% end_if %>
	<% if $Message %>
		<p id="{$FormName}_error" class="alert alert--{$MessageType}">{$Message}</p>
	<% end_if %>

	<% if $Legend %>
		<fieldset>
			<legend>{$Legend}</legend>
	<% end_if %>

	<div class="fieldset">
		<% loop $Fields %>
			{$FieldHolder}
		<% end_loop %>
	</div>

	<% if $Legend %>
		</fieldset>
	<% end_if %>

	<% if $Actions %>
		<div class="form__actions">
			{$Actions}
		</div>
	<% end_if %>
<% if $IncludeFormTag %>
	</form>
<% end_if %>
