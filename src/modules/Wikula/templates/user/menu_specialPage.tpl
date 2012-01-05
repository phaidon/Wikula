<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname=$modinfo.name type='user' func='main'}">{$modinfo.displayname}</a> &#187;
            {gt text="Special_pages" assign=sp}
            {if $tag != $sp}
            <a href="{modurl modname=$modinfo.name type='user' func='show' __tag="Special_pages"}">{gt text="Special pages"}</a> &#187;
            {/if}
            <a href="{modurl modname=$modinfo.name type='user' func='main' tag=$tag|urlencode}">{$tag|hyphen2space|safehtml}</a>
        </h2>
        <div class="z-clearfix">
            <ul class="z-menulinks" style="height: 24px;">
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