<div class="element-two-column">
	<div class="container typography">
		<% if $ShowTitle %>
			<h2>
				{$Title}
			</h2>
		<% end_if %>

		{$HTML}

		<div class="element-two-column__row">
			<div class="element-two-column__column trim">
				<% if $LeftColumnType == 'Image' && $LeftColumnImage %>
					{$LeftColumnImage.FocusFill(576, 324)}
				<% else %>
					{$LeftColumnContent}
				<% end_if %>
			</div>

			<div class="element-two-column__column trim">
				<% if $RightColumnType == 'Image' && $RightColumnImage %>
					{$RightColumnImage.FocusFill(576, 324)}
				<% else %>
					{$RightColumnContent}
				<% end_if %>
			</div>
		</div>
	</div>
</div>
