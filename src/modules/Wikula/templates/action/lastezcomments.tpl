{if $items}
{assign var='olddate' value=''}
  {foreach item='item' from=$items}
    {if $item.titledate neq $olddate}
      <br />
      <strong>{$item.titledate}</strong><br /><br />
      {assign var='olddate' value=$item.titledate}
    {/if}
    {usergetvar name='uname' uid=$item.uid assign='uname'}
    &nbsp;&nbsp;&nbsp; <a href="{modurl modname='Wikula' tag=$item.objectid|urlencode}" title="{$item.objectid}">{$item.objectid}</a>,
    {gt text='Comment by'} {$uname|profilelinkbyuname} ({$item.date})<br />
    &nbsp;&nbsp;&nbsp; <em>{$item.comment|safehtml}</em>
    <br /><br />
  {/foreach}
{else}
  <em>{gt text='No comments yet...'}</em>
{/if}
