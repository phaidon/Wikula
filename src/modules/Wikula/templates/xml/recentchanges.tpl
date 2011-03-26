{* $Id: wikula_xml_recentchanges.tpl 41 2008-10-09 18:29:16Z quan $ *}
{*
{nocache}{php}header("Content-type: application/rss+xml");{/php}{/nocache}
<?xml version="1.0" encoding="{charset}"?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<channel>
<title>{sitename}</title>
<link>{modurl modname='wikula' tag=$smarty.const.RecentChanges}</link>
<description>{gt text='Recent changes of %s' tag1=$sitename}</description>
<language>{pnconfiggetvar name='backend_language'}</language>
{pnconfiggetvar name='site_logo' assign='site_logo'}
{if $site_logo neq ''}
<image>
 <title>{sitename}</title>
 <url>{$baseurl}images/{$site_logo}</url>
 <link>{$baseurl}</link>
</image>
{/if}
*}
{foreach from=$pages item='page'}
<item>
  <title>{$page.tag}</title>
  <link>{modurl modname='wikula' tag=$page.tag|urlencode time=$page.time|urlencode fqurl=true}</link>
  <description>{gt text="(No note added)" assign=def}{gt text='"%1$s" by %2$s on %3$s' tag2=$page.user tag1=$page.note|default:$def tag3=$page.time|pndate_format:'%Y-%m-%d'}</description>
  <pubDate>{$page.time|date_format:"%a, %d %b %Y %H:%M:%S %z"}</pubDate>
</item>
{foreachelse}
<item>
  <title>{gt text='Error'}</title>
  <link>{modurl modname='wikula'}</link>
  <description>{gt text="No items available yet or you're not allowed to access this information"}</description>
</item>
{/foreach}
{*
</channel>
</rss>
*}
