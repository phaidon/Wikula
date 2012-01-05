<div id="wikulaheader">
    <div class="header">
        {specialPageTitle tag=$tag}
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