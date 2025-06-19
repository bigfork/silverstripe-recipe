<head>
	<%-- charset/viewport, meta title and base are critical for everything else --%>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<% base_tag %>
	<title><% if $MetaTitle %>{$MetaTitle.XML}<% else %>{$Title.XML}<% end_if %></title>

	<%-- Vite assets --%>
	<% vite 'src/scss/style.scss', 'src/js/app.js' %>
	<% if $viteIsRunningHot %>
		<% vite '@vite-plugin-svg-spritemap/client__spritemap' %>
	<% end_if %>

	<%-- Metadata --%>
	{$MetaTags(false)}
</head>
