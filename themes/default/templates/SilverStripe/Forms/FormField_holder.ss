<div class="form__field-group<% if $extraClass %> {$extraClass}<% end_if %>" id="{$HolderID}">
	<div class="form__field-holder<% if not $Title %> form__field-holder--no-label<% end_if %>">
		<% if $Title %>
			<label for="{$ID}" class="form__field-label">{$Title}<% if $Required %><em>*</em><% end_if %></label>
		<% end_if %>
		<div class="form__field">
			{$Field}
		</div>
		<% if $Description %><p class="form__field-description">{$Description}</p><% end_if %>
		<% if $Message %><p class="alert alert--{$MessageType}" role="alert">{$Message}</p><% end_if %>
	</div>
	<% if $RightTitle %><p class="form__field-extra-label">{$RightTitle}</p><% end_if %>
</div>
