<header class="header">
	<nav class="nav">
		<ul class="nav__menu">
			<% loop $Menu(1) %>
				<li class="nav__item nav__item--{$LinkingMode}">
					<a class="nav__link" href="{$Link}">{$MenuTitle}</a>
					<% if $Children %>
						<ul class="nav__submenu">
							<% loop $Children %>
								<li class="nav__subitem nav__subitem--{$LinkingMode}">
									<a class="nav__sublink" href="{$Link}">{$MenuTitle}</a>
								</li>
							<% end_loop %>
						</ul>
					<% end_if %>
				</li>
			<% end_loop %>
		</ul>
	</nav>
</header>
