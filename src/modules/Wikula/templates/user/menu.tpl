<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname=$modinfo.name type='user' func='main'}">{$modinfo.displayname}</a> &#187;
            <a href="{modurl modname=$modinfo.name type='user' func='main' tag=$tag|urlencode}">{$tag|hyphen2space|safehtml}</a>
        </h2>
        <div style="float:left;">
        <ul class="z-menulinks" style="height:26px;border-right-width:0px">
            <li>
                <a href="{modurl modname=$modinfo.name type='user' func='edit' tag=$tag|urlencode}" title="{gt text='Edit'}">{gt text='Edit'}</a>
            </li>

            <li>
                <a href="{modurl modname=$modinfo.name type='user' func='history' tag=$tag|urlencode}" title="{gt text='History'}">{gt text='History'}</a>
            </li>
            <li>
                <a href="{modurl modname=$modinfo.name type='user' func='backlinks' tag=$tag|urlencode}" title="{gt text='Backlinks'}">{gt text='Backlinks'}</a>
            </li>

            <li>
                &nbsp;&nbsp;
            </li>

            <li>
                <a href="{modurl modname=$modinfo.name type='user' func='main' tag='Special_pages'}" title="{gt text='Special pages'}">{gt text='Special pages'}</a>
            </li>

            {if $coredata.logged_in eq false}
            <li>
                <a href="{modurl modname='Users' func='loginscreen'}" title="">{gt text='Log in'}</a>
            </li>
            {/if}

        </ul>
        </div>
        <div class="wikula_menu">
        <form class="z-form" action="{modurl modname=$modinfo.name type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">


            <input id="wikula_phrase" name="phrase" size="12" class="wikula_searchbox"/>
            <button id="searchButton" type="submit" name="button" class="wikula_searchbutton">
                {img modname=core src=search.png set=icons/extrasmall width="12" height=12" alt=""} 
            </button>
        </form>
        </div>

        <div style="clear:both;"></div>

        {insert name='getstatusmsg'}
    </div>
</div>
