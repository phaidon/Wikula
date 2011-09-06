{* $Id: wikula_block_wkrecentchanges.tpl 41 2008-10-09 18:29:16Z quan $ *}

<div class="wk-block-recentchanges">

<p style="float: right;">
  <a href="{modurl modname='wikula' func='recentchangesxml' theme='rss'}" title="RSS">{img modname='wikula' src='rss.png' __title='RSS' __alt='RSS'}</a>
</p>

{if $pagelist}
  {assign var='currentdate' value=''}
  {foreach from=$pagelist key='date' item='pages'}
    <ul class="recentchanges">
    {foreach from=$pages item='page'}
      <li>
      <a href="{modurl modname='wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
      <span class="pn-sub">{gt text='by' comment="e.g. written by Drak"}</span> {$page.user} <span class="pn-sub">({$page.time|dateformat:'%b %d'})</span>
      {if $page.note neq ''}<br /><span class="pagenote">[ {$page.note} ]</span>{/if}
      </li>
    {/foreach}
    </ul>
  {foreachelse}
    {gt text='There are no recent changes'}
  {/foreach}
{/if}

</div>
