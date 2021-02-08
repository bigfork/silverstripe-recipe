<div id="{$HolderID}" class="form__field-group field<% if $extraClass %> {$extraClass}<% end_if %>">
	<div class="form__field-holder">
		<% if $Title && $RightTitle %>
			<label for="{$ID}" class="form__field-label">{$Title}<% if $Required %><em>*</em><% end_if %></label>
		<% end_if %>
		<div class="form__field form-check">
			{$Field}
			<label class="form-check-label" for="{$ID}"><% if $RightTitle %>{$RightTitle}<% else %>{$Title}<% end_if %></label>
		</div>
		<% if $Description %><p class="form__field-description form-text" id="describes-{$ID}">{$Description}</p><% end_if %>
		<% if $Message %><p class="alert alert--{$MessageType}" role="alert" id="message-{$ID}">{$Message}</p><% end_if %>
	</div>
</div>
