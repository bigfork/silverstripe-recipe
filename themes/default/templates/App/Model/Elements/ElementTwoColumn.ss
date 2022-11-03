<div class="element-two-column">
	<% include App\Model\Elements\Includes\Before %>

	<div class="container typography">
		<div class="element-two-column__row">
			<div class="element-two-column__column trim">
				<% if $LeftColumnType == 'Image' && $LeftColumnImage %>
					<% if $LeftColumnImageCrop %>
						{$LeftColumnImage.FocusFill(576, 324)}
					<% else %>
						{$LeftColumnImage.ScaleWidth(576)}
					<% end_if %>
				<% else %>
					{$LeftColumnContent}
				<% end_if %>
			</div>

			<div class="element-two-column__column trim">
				<% if $RightColumnType == 'Image' && $RightColumnImage %>
					<% if $RightColumnImageCrop %>
						{$RightColumnImage.FocusFill(576, 324)}
					<% else %>
						{$RightColumnImage.ScaleWidth(576)}
					<% end_if %>
				<% else %>
					{$RightColumnContent}
				<% end_if %>
			</div>
		</div>
	</div>

	<% include App\Model\Elements\Includes\After %>
</div>
