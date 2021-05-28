<picture>
	<%-- video tag is needed for IE9 support - see https://scottjehl.github.io/picturefill/ --%>
	<!--[if IE 9]><video style="display: none;"><![endif]-->
	<% loop $Sizes %>
	<source media="{$Query}" srcset="{$Image.URL}" width="{$Image.Width}" height="{$Image.Height}">
	<% end_loop %>
	<!--[if IE 9]></video><![endif]-->
	<img src="{$DefaultImage.URL}"<% if $ExtraClasses %> class="{$ExtraClasses}"<% end_if %> alt="{$Title}" width="{$DefaultImage.Width}" height="{$DefaultImage.Height}" loading="lazy" decoding="async">
</picture>
<noscript>
	<img src="{$DefaultImage.URL}"<% if $ExtraClasses %> class="{$ExtraClasses}"<% end_if %> alt="{$Title}" width="{$DefaultImage.Width}" height="{$DefaultImage.Height}" loading="lazy" decoding="async">
</noscript>
