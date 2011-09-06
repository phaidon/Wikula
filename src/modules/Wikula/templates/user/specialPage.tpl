<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname='Wikula' type='user' func='main'}">{$modinfo.displayname}</a> &#187;
            <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode}">
                {$tag|safehtml}
            </a>
        </h2>
        {insert name='getstatusmsg'}
    </div>
</div>

<div id="Wikula">
    <div class="page">{$content}</div>
    {include file='user/footer.tpl'}
</div>
