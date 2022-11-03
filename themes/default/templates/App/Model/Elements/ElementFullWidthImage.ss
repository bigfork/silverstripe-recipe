<div class="element-full-width-image">
	<div class="container">
		<div class="typography">
			<% if $ShowTitle %>
				<h2>
					{$Title}
				</h2>
			<% end_if %>

			{$HTML}
		</div>

		<% if $Images.Count > 1 %>
			<div class="splide element-full-width-image__carousel" data-element-full-width-carousel>
				<div class="splide__track">
					<ul class="splide__list">
						<% loop $Images %>
							<li class="splide__slide">
								<div class="element-full-width-image__image">
									{$FocusFill(1128, 768)}
								</div>
							</li>
						<% end_loop %>
					</ul>
				</div>
			</div>
		<% else_if $Images.Count = 1 %>
			<div class="element-full-width-image__image">
				{$Images.First.FocusFill(1128, 768)}
			</div>
		<% end_if %>
	</div>
</div>