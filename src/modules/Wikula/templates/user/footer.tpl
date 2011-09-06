<div class="wiki_footer">
    <div style="text-align:left; padding:4px;">
        <form action="{modurl modname='wikula' type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <a href="{modurl modname='wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
                <span class="text_separator">::</span>
                {gt text='Revisions of %s Feed' tag1=$tag assign='altrssfeed'}
                <a href="{modurl modname='wikula' tag=$tag|urlencode time=$backpage.time|urlencode}" class="datetime">{$backpage.time|safehtml}</a> <a href="{modurl modname='wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss'}" title="{$altrssfeed|safehtml}">{img src='rss.png' alt=$altrssfeed modname='wikula'}</a>
                <span class="text_separator">::</span>
                {gt text='Owner'}: {$backpage.owner|profilelinkbyuname}
                <span class="text_separator">::</span>
                <label for="wikula_phrase">{gt text='Search for'}
                    <input id="wikula_phrase" name="phrase" size="12" class="searchbox" />
                </label>
            </div>
        </form>
    </div>
</div>