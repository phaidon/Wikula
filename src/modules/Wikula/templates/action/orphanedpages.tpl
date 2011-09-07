<div class="action_pageindex">

<p>{gt text='The following list shows those pages held in the Wiki that are not linked to on any other pages.'}</p>
<br />
{if !empty($items)}
  <ul>
    {foreach from=$items item='item'}
    <li><a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="{$item.tag|safehtml}">{$item.tag|safehtml}</a></li>
    {/foreach}
  </ul>
{else}
  <em>{gt text='No orphaned pages! Good!'}</em>
{/if}

</div>
