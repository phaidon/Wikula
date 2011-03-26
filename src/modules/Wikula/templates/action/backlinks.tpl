{* $Id: action/backlinks.tpl 41 2008-10-09 18:29:16Z quan $ *}

{if $pages}
  <h5>{gt text='Backlinks'}</h5>
  <blockquote>
  {foreach item='page' from=$pages}
    <a href="{modurl modname='wikula' func='main' tag=$page|urlencode}" title="{$page}">{$page}</a><br />
  {/foreach}
  </blockquote>
{/if}
