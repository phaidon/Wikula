<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname='Wikula' type='user' func='main'}">{$modinfo.displayname}</a> &#187;
            <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode}">{$tag|safehtml}</a>
        </h2>
        <ul class="z-menulinks">
            <li>
                {if $tag neq $modvars.Wikula.root_page}
                <a href="{modurl modname='Wikula'}" title="{$modvars.Wikula.root_page}">{$modvars.Wikula.root_page}</a>
                {else}
                {$modvars.Wikula.root_page}
                {/if}
            </li>

            <li>
                {if $tag neq $smarty.const.CategoryCategory}
                <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.CategoryCategory}" title="">{gt text='Categories'}</a>
                {else}
                {gt text='Categories'}
                {/if}
            </li>

            <li>
                {if $tag neq $smarty.const.PageIndex}
                <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.PageIndex}" title="">{gt text='Page index'}</a>
                {else}
                <span>{gt text='Page index'}</span>
                {/if}
            </li>

            <li>
                {if $tag neq $smarty.const.TextSearch}
                <a href="{modurl modname='Wikula' type='user' __tag='Search'}" title="">{gt text='Search'}</a>
                {else}
                <span>{gt text='Search'}</span>
                {/if}
            </li>

            <li>
                {if $tag neq $smarty.const.WikiHelp}
                <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.WikiHelp}" title="">{gt text='Help'}</a>
                {else}
                <span>{gt text='Help'}</span>
                {/if}
            </li>

            {userloggedin assign='islogged'}
            {if $islogged eq false}
            <li>
                <a href="{modurl modname='Users' func='loginscreen'}" title="">{gt text='Log in'}</a>
            </li>
            {/if}

            <li>
                {if $tag neq $smarty.const.RecentChanges}
                <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.RecentChanges}" title="">{gt text='Recent changes'}</a>
                {else}
                <span>{gt text='Recent changes'}</span>
                {/if}
            </li>

            <li><a href="{modurl modname='Wikula' func='recentchangesxml' theme='rss'}" title="{gt text='Recent changes Feed'}">{img modname='Wikula' src='rss.png' __title='Recent changes Feed'  __alt='RSS'}</a></li>
        </ul>

        {insert name='getstatusmsg'}
    </div>
</div>