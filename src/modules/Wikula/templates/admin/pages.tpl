{gt text='Page Index' assign=templatetitle}
{gt text='view' assign=templateicon}
{include file='admin/header.tpl'}


<div>
{if $pagelist}
  <strong><a href="{modurl modname='Wikula' type='admin' func='pages'}" title="{gt text='All'}">{gt text='All'}</a></strong>&nbsp;&nbsp;
  {foreach item='letter' from=$headerletters}
    <strong><a href="{modurl modname='Wikula' type='admin' func='pages' letter=$letter}" title="{$letter}">{$letter}</a></strong>
  {/foreach}
  <br />
  {assign var='currentchar' value=''}


  {foreach item='letter' from=$pagelist key='firstchar'}
    {if $currentchar neq $firstchar}
      {assign var='currentchar' value=$firstchar}
      <br /><strong>{$firstchar}</strong><br />
    {/if}
    {foreach item='page' from=$letter}
      &nbsp;&nbsp;&nbsp;<a href="{modurl modname='Wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
      {if $page.owner neq '(Public)' and $page.owner neq ''}
        {if $page.owner eq $username}
          *
        {else}
          . . . . {gt text='Owner:'} {$page.owner}
        {/if}
      {/if}
      <br />
    {/foreach}
  {/foreach}
  <br />

  {if $userownspages}{gt text='Items marked with a * indicate pages that you own.'}<br />{/if}
{else}
  <span class="error">{gt text='No page found'}</span>
{/if}
</div>

</div>
