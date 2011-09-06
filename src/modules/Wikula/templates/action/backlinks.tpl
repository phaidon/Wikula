{if $pages}
<h5>{gt text='Backlinks'}</h5>
<blockquote>
    {foreach item='page' from=$pages}
    <a href="{modurl modname='Wikula' func='main' tag=$page|urlencode}" title="{$page}">{$page}</a><br />
    {/foreach}
</blockquote>
{/if}
