{if $items}
{assign var='olddate' value=''}
  {foreach item='item' from=$items}
    {if $item.titledate neq $olddate}
      <br />
      <strong>{$item.titledate}</strong><br /><br />
      {assign var='olddate' value=$item.titledate}
    {/if}
    &nbsp;&nbsp;&nbsp; <a href="{modurl modname='Wikula' tag=$item.objectid|urlencode}" title="{$item.objectid|safetext}">{$item.objectid|safetext}</a>,
    {gt text='Comment by %1$s on %2$s:' tag1=$uname|profilelinkbyuid tag2=$item.date|safehtml}
    &nbsp;&nbsp;&nbsp; <em>{$item.comment|safehtml}</em>
    <br /><br />
  {/foreach}
{else}
  <em>{gt text='No comments yet...'}</em>
{/if}
