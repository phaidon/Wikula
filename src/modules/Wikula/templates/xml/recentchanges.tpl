{foreach from=$pages item='page'}
<item>
  <title>{$page.tag}</title>
  <link>{modurl modname='Wikula' type='user' func='show' tag=$page.tag|urlencode time=$page.time->getTimestamp() fqurl=true}</link>
  <description>{gt text="(No note added)" assign=def}{gt text='"%1$s" by %2$s on %3$s' tag2=$page.user tag1=$page.note|default:$def tag3=$page.time|dateformat:'%Y-%m-%d'}</description>
  <pubDate>{$page.time|dateformat:"%a, %d %b %Y %H:%M:%S %z"}</pubDate>
</item>
{foreachelse}
<item>
  <title>{gt text='Error'}</title>
  <link>{modurl modname='Wikula' type='user' func='main'}</link>
  <description>{gt text="No items available yet or you're not allowed to access this information"}</description>
</item>
{/foreach}
