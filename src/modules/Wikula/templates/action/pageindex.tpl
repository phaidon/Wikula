<div class="action_pageindex">

<div class="floatl z-clearfix">
{gt text='This is an alphabetical list of pages you can read on this server.'}
{if $userownspages}
{gt text='Items marked with a * indicate pages that you own.'}
{/if}
</div>
<br />

<strong><a href="{modurl modname='Wikula' type='user' func='main' tag=$currentpage|urlencode}" title="{gt text='All'}">{gt text='All'}</a></strong>
{foreach item='letter' from=$headerletters}
  &nbsp;&nbsp;<strong><a href="{modurl modname='Wikula' type='user' func='main' letter=$letter tag=$currentpage|urlencode}" title="{$letter}">{$letter}</a></strong>
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
    &nbsp;&nbsp;&nbsp;<a href="{modurl modname='Wikula' type='user' func='main' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.title}</a>
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
