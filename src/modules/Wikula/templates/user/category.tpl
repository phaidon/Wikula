<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname=$modinfo.name type='user' func='main'}">
                {$modinfo.displayname}
            </a> &#187;
            <a href="{modurl modname=$modinfo.name type='user' func='categories'}">{gt text="Categories"}</a>
            &#187;
            {$category|hyphen2space|safehtml}
        </h2>
        {include file='user/menulight.tpl'}
        {insert name='getstatusmsg'}
    </div>
</div>

<p class="z-informationmsg">
    {gt text='Pages that belong to this category:'}
</p>

<div id="wikula">
    <div class="page">
        {letterList pages=$pages}
    </div>
</div>