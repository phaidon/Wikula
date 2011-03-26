{* $Id: action/orphanedpages.tpl 41 2008-10-09 18:29:16Z quan $ *}

<div class="action_pageindex">

<h3>{gt text='Orphaned Pages'}</h3>

<h4>{gt text='The following list shows those pages held in the Wiki that are not linked to on any other pages.'}</h4>
{if !empty($items)}
  <ul>
    {foreach from=$items item='item'}
    <li><a href="{modurl modname='wikula' tag=$item.tag|urlencode}" title="{$item.tag|safehtml}">{$item.tag|safehtml}</a></li>
    {/foreach}
  </ul>
{else}
  <em>{gt text='No orphaned pages! Good!'}</em>
{/if}

</div>
