<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname=$modinfo.name type='user' func='main'}">{$modinfo.displayname}</a> &#187;
            <a href="{modurl modname=$modinfo.name type='user' func='main' tag=$tag|urlencode}">{$tag|hyphen2space|safehtml}</a>
        </h2>
        <div class="z-clearfix">
            <ul class="z-menulinks" style="height:24px">
                {modapifunc modname=$modinfo.name type='user' func='isAllowedToEdit' tag=$tag assign='isAllowedToEdit'}
                {if $isAllowedToEdit}
                <li><a href="{modurl modname=$modinfo.name type='user' func='edit' tag=$tag|urlencode}" title="{gt text='Edit'}">{gt text='Edit'}</a></li>
                {/if}
                {modapifunc modname=$modinfo.name type='SpecialPage' func='isSpecialPage' tag=$tag assign='isSpecialPage'}
                {if !$isSpecialPage}
                <li><a href="{modurl modname=$modinfo.name type='user' func='history' tag=$tag|urlencode}" title="{gt text='History'}">{gt text='History'}</a></li>
                <li><a href="{modurl modname=$modinfo.name type='user' func='backlinks' tag=$tag|urlencode}" title="{gt text='Backlinks'}">{gt text='Backlinks'}</a></li>
                {/if}
                <li><a href="{modurl modname=$modinfo.name type='user' func='show' tag='Special_pages'}" title="{gt text='Special pages'}">{gt text='Special pages'}</a></li>
                {if $coredata.logged_in eq false}
                <li><a href="{modurl modname='Users' func='loginscreen'}" title="{gt text='Log in'}">{gt text='Log in'}</a></li>
                {/if}
                <li class="z-floatright" style=" border-right: none;">
                    <form class="z-form" action="{modurl modname=$modinfo.name type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">
                        <div>
                            <input id="wikula_phrase" name="phrase" size="12" class="wikula_searchbox"/>
                            <button id="searchButton" type="submit" name="button" class="wikula_searchbutton">{img src='search.png' alt='' modname=$modinfo.name width="14" height="14"}</button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>
        {insert name='getstatusmsg'}
    </div>
</div>