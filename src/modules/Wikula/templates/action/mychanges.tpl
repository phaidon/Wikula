<div class="action_mychanges">

<div class="floatl z-clearfix">
{if $alpha eq 1}
    {gt text='This is a list of pages you'}(<a href="{modurl modname='wikula' tag=$tag|urlencode}" title="{gt text='order by date'}">{gt text='order by date'}</a>).
{else}
    {gt text='This is a list of pages you'}(<a href="{modurl modname='wikula' tag=$tag|urlencode alpha=1}" title="{gt text='order alphabetically'}">{gt text='order alphabetically'}</a>).
{/if}
</div>

{if $editcount eq 0}
    <em>{gt text='This is a list of pages you'}</em>
{else}
    {assign var='currentkey' value=''}

    {foreach name='mychanges' from=$pagelist item='pages' key='key'}
      {if $currentkey neq $key}
        {assign var='currentkey' value=$key}
        <h5>{$key|pndate_format:'datelong'}</h5>
      {/if}
      <span class="mychanges">
      {foreach from=$pages item='page'}
        &nbsp;&nbsp;&nbsp;
        (<a href="{modurl modname='wikula' tag=$page.tag|urlencode time=$page.time|urlencode}" title="{gt text='Revisions'}">{$page.timeformatted}</a>)
        (<a href="{modurl modname='wikula' func='history' tag=$page.tag|urlencode}" title="{$page.tag} {gt text='History'}">{gt text='History'}</a>)
        <a href="{modurl modname='wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
        {if !$alpha}
        &nbsp;<span class="pagenote">[&nbsp;{$page.note}&nbsp;]</span>
        {/if}
        {if $smarty.foreach.mychanges.last ne true}
        <br />
        {/if}
      {/foreach}
      </span>
      {if $smarty.foreach.mychanges.last ne true}
      <br />
      {/if}
    {/foreach}
{/if}

</div>
