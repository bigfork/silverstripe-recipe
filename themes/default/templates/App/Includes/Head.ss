<head>
	<%-- charset/viewport, meta title and base are critical for everything else --%>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<% base_tag %>
	<title><% if $MetaTitle %>{$MetaTitle.XML}<% else %>{$Title.XML} | {$SiteConfig.Title}<% end_if %></title>

	<%-- Synchronous scripts --%>

	<%-- Stylesheets --%>
	<link rel="stylesheet" href="{$themedResourceURL('dist/css/style.css')}">

	<%-- Asynchronous/deferred scripts --%>
	<script type="text/javascript" src="{$themedResourceURL('dist/js/app.js')}" defer></script>

	<%-- Metadata --%>
	{$MetaTags(false)}
</head>
