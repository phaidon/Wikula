<div class="wikula_block_recentchanges">

    <a class="z-icon-es-rss" href="{modurl modname='Wikula' func='recentchangesxml' theme='rss'}" title="RSS">{gt text="RSS Feed"}</a>

    {if $pagelist}
    {assign var='currentdate' value=''}
    {foreach from=$pagelist key='date' item='pages'}
    <ul class="recentchanges">
        {foreach from=$pages item='page'}
        <li>
            <a href="{modurl modname='Wikula' tag=$page.tag|urlencode}" title="{$page.tag|safehtml}">{$page.tag|safehtml}</a>
            <span class="z-sub">{gt text='by %s' tag1=$page.user|safehtml} ({$page.time|dateformat:'%b %d'})</span>
            {if $page.note neq ''}<br /><span class="pagenote">[ {$page.note} ]</span>{/if}
        </li>
        {/foreach}
    </ul>
    {foreachelse}
    <ul><li>{gt text='There are no recent changes'}</li></ul>
    {/foreach}
    {/if}

</div>
