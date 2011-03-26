{* $Id: action/pageindex.tpl 58 2008-11-14 21:10:52Z arg $ *}

<div class="action_pageindex">

<h3>{gt text='Page Index'}</h3>

<div class="floatl">
{gt text='This is an alphabetical list of pages you can read on this server.'}
{if $userownspages}
{gt text='Items marked with a * indicate pages that you own.'}
{/if}
</div>
<div class="clear"></div>
<br />

<strong><a href="{modurl modname='wikula' tag=$currentpage|urlencode}" title="{gt text='All'}">{gt text='All'}</a></strong>
{foreach item='letter' from=$headerletters}
  &nbsp;&nbsp;<strong><a href="{modurl modname='wikula' letter=$letter tag=$currentpage|urlencode}" title="{$letter}">{$letter}</a></strong>
{/foreach}
<br /><br />

{assign var='currentchar' value=''}

{foreach from=$pagelist key='firstchar' item='pagesinletter'}
  {if $currentchar neq $firstchar}
    {if !empty($currentchar)}<br />{/if}
    {assign var='currentchar' value=$firstchar}
    <strong>{$firstchar}</strong>
    <br />
  {/if}
  {foreach from=$pagesinletter item='page'}
    &nbsp;&nbsp;&nbsp;<a href="{modurl modname='wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{getTitleByTag tag=$page.tag body=$page.body}</a>
    {if $page.owner neq '(Public)' and $page.owner neq ''}
      {if $page.owner eq $username}
        *
      {else}
        {gt text='Owner'}: {$page.owner}
      {/if}
    {/if}
    <br />
  {/foreach}
{/foreach}

</div>
