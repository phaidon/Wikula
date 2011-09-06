<div class="action_pageindex">

    <p class="z-informationmsg">
        {gt text='This is an alphabetical list of pages you can read on this server.'}
        {if $userownspages}
        {gt text='Items marked with a * indicate pages that you own.'}
        {/if}
    </p>

    <p class="z-gap">
        <strong><a href="{modurl modname='Wikula' type='user' func='main' tag=$currentpage|urlencode}" title="{gt text='All'}">{gt text='All'}</a></strong>
        {foreach item='letter' from=$headerletters}
        &nbsp;&nbsp;<strong><a href="{modurl modname='Wikula' type='user' func='main' letter=$letter tag=$currentpage|urlencode}" title="{$letter|safehtml}">{$letter|safehtml}</a></strong>
        {/foreach}
    </p>

    {assign var='currentchar' value=''}

    {foreach from=$pagelist key='firstchar' item='pagesinletter'}
    {if $currentchar neq $firstchar}
    {if !empty($currentchar)}<br />{/if}
    {assign var='currentchar' value=$firstchar}
    <strong>{$firstchar|safehtml}</strong>
    <br />
    {/if}
    {foreach from=$pagesinletter item='page'}
    &nbsp;&nbsp;&nbsp;<a href="{modurl modname='Wikula' type='user' func='main' tag=$page.tag|urlencode}" title="{$page.tag|safehtml}">{$page.title|safehtml}</a>
    {if $page.owner neq '(Public)' and $page.owner neq ''}
    {if $page.owner eq $username}
    *
    {else}
    {gt text='Owner: %s' tag1=$page.owner|profilelinkbyuname}
    {/if}
    {/if}
    <br />
    {/foreach}
    {/foreach}

</div>
