{modapifunc modname=$modinfo.name type='user' func='isAllowedToEdit' tag=$tag assign='isAllowedToEdit'}

<div id="wikulaheader">
    <div class="header">
        {wikulaPageTitle tag=$tag}
        <div class="z-clearfix">
            <ul class="z-menulinks" style="height: 24px;">
                {if $isAllowedToEdit}
                <li><a href="{modurl modname=$modinfo.name type='user' func='edit' tag=$tag|urlencode}" title="{gt text='Edit'}">{gt text='Edit'}</a></li>
                {/if}
                <li><a href="{modurl modname=$modinfo.name type='user' func='history' tag=$tag|urlencode}" title="{gt text='History'}">{gt text='History'}</a></li>
                <li><a href="{modurl modname=$modinfo.name type='user' func='backlinks' tag=$tag|urlencode}" title="{gt text='Backlinks'}">{gt text='Backlinks'}</a></li>

                <li><a href="{modurl modname=$modinfo.name type='user' func='categories'}" title="{gt text='Categories'}">{gt text='Categories'}</a></li>                
                <li><a href="{modurl modname=$modinfo.name type='user' func='show' __tag='Special_pages'}" title="{gt text='Special pages'}">{gt text='Special pages'}</a></li>
                {if $coredata.logged_in eq false}
                <li><a href="{modurl modname='Users' type='user' func='loginscreen'}" title="{gt text='Log in'}">{gt text='Log in'}</a></li>
                {/if}
                <li class="z-floatright" style="border-right: none;">
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