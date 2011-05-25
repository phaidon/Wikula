<div class="action_recentchanges">

<p style="float: right;">
  <a href="{modurl modname='wikula' func='recentchangesxml' theme='rss'}" title="{gt text='Recent Changes'}">{img modname='wikula' src='rss.png' __title='Recent changes Feed'  __alt='RSS'}</a>
</p>

{if $pagelist}
  {assign var='currentdate' value=''}
  {foreach from=$pagelist key='date' item='pages'}
    {if $currentdate neq $date}
      {if !empty($currentdate)}<br />{/if}
      {assign var='currentdate' value=$date}
      <strong>{$date}</strong>
      <br />
    {/if}
    <span class="recentchanges">
    {foreach from=$pages item='page'}
      &nbsp;&nbsp;&nbsp;&nbsp;
      (<a href="{modurl modname='wikula' tag=$page.tag|urlencode time=$page.time|urlencode}" title="{gt text='Recent Changes Revisions'}">{$page.timeformatted}</a>)
      [<a href="{modurl modname='wikula' func='history' tag=$page.tag|urlencode}" title="{$page.tag} {gt text='History'}">{gt text='History'}</a>] -
      <a href="{modurl modname='wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
      &rArr; {$page.user} <span class="pagenote">[ {$page.note} ]</span>
      <br />
    {/foreach}
    </span>
  {foreachelse}
    {gt text='There are no recent changes'}
  {/foreach}
{/if}

</div>
