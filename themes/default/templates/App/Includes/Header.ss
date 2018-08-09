<header class="header" role="banner">
	<nav class="nav">
		<ul class="nav__menu" role="menubar">
			<% loop $Menu(1) %>
				<li class="nav__item nav__item--{$LinkingMode}">
					<a class="nav__link" href="{$Link}" role="menuitem"<% if $Children %> id="nav__menu--n{$Pos}" aria-haspopup="true"<% end_if %>>{$MenuTitle}</a>
					<% if $Children %>
						<ul class="nav__submenu" aria-labelledby="nav__menu--n{$Pos}" role="menu">
							<% loop $Children %>
								<li class="nav__subitem nav__subitem--{$LinkingMode}">
									<a class="nav__sublink" href="{$Link}" role="menuitem">{$MenuTitle}</a>
								</li>
							<% end_loop %>
						</ul>
					<% end_if %>
				</li>
			<% end_loop %>
		</ul>
	</nav>
</header>
