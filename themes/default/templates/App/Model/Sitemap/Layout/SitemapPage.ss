
<main class="container typography">
	<h1>
		{$Title}
	</h1>

	{$Content}

	<% include App\Model\Sitemap\Includes\PageList Pages=$Menu(1),TopLevel=1 %>
</main>
