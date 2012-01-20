<div class="action_mychanges">
    <p class="z-informationmsg">
        {if $alpha eq 1}
        {gt text='This is a list of pages you'} (<a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode}" title="{gt text='Order by date'}">{gt text='Order by date'}</a>).
        {else}
        {gt text='This is a list of pages you'} (<a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode alpha=1}" title="{gt text='Order alphabetically'}">{gt text='Order alphabetically'}</a>).
        {/if}
    </p>

    {if $editcount eq 0}
    <em>{gt text='This is a list of pages you'}</em>
    {else}
    {assign var='currentkey' value=''}
    {foreach name='mychanges' from=$pagelist item='pages' key='key'}
    {if $currentkey neq $key}
    {assign var='currentkey' value=$key}
    <h5>{$key|dateformat:'datelong'}</h5>
    {/if}

    <ul class="mychanges">
        {foreach from=$pages item='page'}
        <li>
            (<a href="{modurl modname='Wikula' type='user' func='main'    tag=$page.tag|urlencode id=$page.id}" title="{gt text='Revisions'}">{$page.timeformatted}</a>)
            (<a href="{modurl modname='Wikula' type='user' func='history' tag=$page.tag|urlencode}" title="{$page.tag} {gt text='History'}">{gt text='History'}</a>)
             <a href="{modurl modname='Wikula' type='user' func='show'    tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
            {if !$alpha}
            &nbsp;<span class="pagenote">[&nbsp;{$page.note}&nbsp;]</span>
            {/if}
        </li>
        {/foreach}
    </ul>

    {/foreach}
    {/if}
</div>