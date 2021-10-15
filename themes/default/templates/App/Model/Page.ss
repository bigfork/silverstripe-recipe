<!DOCTYPE html>
<html lang="{$ContentLocale}">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link href="//www.google-analytics.com" rel="dns-prefetch" />

	<title><% if $MetaTitle %>{$MetaTitle.XML}<% else %>{$Title.XML} | {$SiteConfig.Title}<% end_if %></title>
	<% base_tag %>
	{$MetaTags(false)}

	<% require themedCSS('dist/css/style') %>
	<% include App\Includes\OpenGraph %>
</head>
<body class="{$ClassName.ShortName.LowerCase}">

<div class="viewport">

	<% include App\Includes\Header %>

	{$Layout}

	<% include App\Includes\Footer %>

</div>

</body>
</html>
