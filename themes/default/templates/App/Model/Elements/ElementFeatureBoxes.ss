<div class="element-feature-boxes">
	<% include App\Model\Elements\Includes\Before %>

	<div class="container">
		<% if $FeatureBoxes %>
			<ul class="element-feature-boxes__grid">
				<% loop $FeatureBoxes %>
					<li class="element-feature-boxes__column">
						<div class="element-feature-boxes__card typography">
							<% if $Image %>
								{$Image.FocusFill(576, 324)}
							<% end_if %>

							<div class="element-feature-boxes__card-content">
								<% if $Title %>
									<h3>
										{$Title}
									</h3>
								<% end_if %>

								<% if $Content %>
									<p>
										{$Content}
									</p>
								<% end_if %>

								<% if $Link %>
									<a
										href="{$Link}"
										<% if $LinkTarget %>target="{$LinkTarget}"<% end_if %>
										<% if $LinkType == 'File' %> download<% end_if %>
									>
										{$LinkText}
									</a>
								<% end_if %>
							</div>
						</div>
					</li>
				<% end_loop %>
			</ul>
		<% end_if %>
	</div>

	<% include App\Model\Elements\Includes\After %>
</div>
