<!DOCTYPE html>
<html lang="{$ContentLocale}" class="nojs">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link href="//www.google-analytics.com" rel="dns-prefetch" />
	<link href="//ajax.googleapis.com" rel="dns-prefetch" />

	<title><% if MetaTitle %>{$MetaTitle.XML}<% else %>{$Title.XML} | {$SiteConfig.Title}<% end_if %></title>
	<% base_tag %>
	{$MetaTags(false)}

	<% require themedCSS('style') %>

	<script type="text/javascript">
	(function(H){H.className=H.className.replace(/\\bnojs\\b/,'')})(document.documentElement)
	</script>
</head>
<body class="{$ClassName.LowerCase}">

<div class="viewport">

	<% include Header %>

	{$Layout}

	<% include Footer %>

</div>

<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

</body>
</html>
