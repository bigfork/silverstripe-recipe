<div class="form__field-group<% if $extraClass %> {$extraClass}<% end_if %>" id="{$HolderID}">
	<div class="form__field-holder<% if not $Title %> form__field-holder--no-label<% end_if %>" >
		<% if $Title %>
			<div id="{$ID}_GroupLabel" class="form__field-label">{$Title}<% if $Required %><em>*</em><% end_if %></div>
		<% end_if %>
		<div class="form__field" role="radiogroup" aria-labelledby="{$ID}_GroupLabel">
			{$Field}
		</div>
		<% if $Description %><p class="form__field-description">{$Description}</p><% end_if %>
		<% if $Message %><p class="alert alert--{$MessageType}" role="alert">{$Message}</p><% end_if %>
	</div>
	<% if $RightTitle %><p class="form__field-extra-label">{$RightTitle}</p><% end_if %>
</div>
