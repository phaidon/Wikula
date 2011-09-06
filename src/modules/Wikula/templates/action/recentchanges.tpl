<div class="action_recentchanges">
    <p class="z-clearfix"><a class="z-icon-es-rss z-floatright" href="{modurl modname='Wikula' func='recentchangesxml' theme='rss'}" title="{gt text='Recent Changes'}">{gt text='Recent Changes'}</a></p>
    {if $pagelist}
    {assign var='currentdate' value=''}
    {foreach from=$pagelist key='date' item='pages'}
    {if $currentdate neq $date}
    {if !empty($currentdate)}<br />{/if}
    {assign var='currentdate' value=$date}
    <h5>{$date}</h5>
    {/if}
    <ul>
        {foreach from=$pages item='page'}
        <li>
            (<a href="{modurl modname='Wikula' tag=$page.tag|urlencode time=$page.time|urlencode}" title="{gt text='Recent Changes Revisions'}">{$page.timeformatted}</a>)
            [<a href="{modurl modname='Wikula' func='history' tag=$page.tag|urlencode}" title="{$page.tag} {gt text='History'}">{gt text='History'}</a>] -
            <a href="{modurl modname='Wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
            &rArr; {$page.user} <span class="pagenote">[ {$page.note} ]</span>
        </li>
        {foreachelse}
        <li>&nbsp;</li>
        {/foreach}
    </ul>
    {foreachelse}
    <h5>{gt text='There are no recent changes'}</h5>
    {/foreach}
    {/if}
</div>
