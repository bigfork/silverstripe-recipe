<ul>
	<% loop $Pages %>
		<li>
			<% if $Up.TopLevel %>
				<h2>
					<a href="{$Link}">{$MenuTitle}</a>
				</h2>
			<% else %>
				<a href="{$Link}">{$MenuTitle}</a>
			<% end_if %>

			<% if $Children %>
				<% include App\Model\Sitemap\Includes\PageList Pages=$Children %>
			<% end_if %>
		</li>
	<% end_loop %>
</ul>
