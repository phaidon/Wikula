{if $pages}
<h5>{gt text='Backlinks'}</h5>
<ul>
    {foreach item='page' from=$pages}
    <li><a href="{modurl modname='Wikula' func='main' tag=$page|urlencode}" title="{$page|safehtml}">{$page|safehtml}</a></li>
    {/foreach}
</ul>
{/if}
