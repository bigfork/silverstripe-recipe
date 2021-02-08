<% loop $Options %>
	<div class="$Class" role="radio">
		<input id="$ID" class="radio" name="$Name" type="radio" value="$Value"
			<% if $isChecked %>checked<% end_if %>
			<% if $isDisabled %>disabled<% end_if %>
			<% if $Up.Required %>required<% end_if %> />
		<label for="$ID">$Title</label>
	</div>
<% end_loop %>
