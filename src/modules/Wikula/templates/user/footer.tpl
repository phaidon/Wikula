<form class="z-form" action="{modurl modname='Wikula' type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset class="wikula_alignmiddle z-clearfix">
        <a href="{modurl modname='Wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
        <span class="text_separator">::</span>
        {gt text='Revisions of %s Feed' tag1=$tag assign='altrssfeed'}
        <a href="{modurl modname='Wikula' tag=$tag|urlencode time=$showpage.time|urlencode}" class="datetime">{$showpage.time|safehtml}</a> <a href="{modurl modname='Wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss'}" title="{$altrssfeed|safehtml}">{img src='rss.png' alt=$altrssfeed modname='Wikula'}</a>
        <span class="text_separator">::</span>
        {gt text='Owner'}: {$showpage.owner|profilelinkbyuname}
        <span class="text_separator">::</span>
        <label for="wikula_phrase">{gt text='Search for'}</label>
        <input id="wikula_phrase" name="phrase" size="12" class="searchbox" />
    </fieldset>
</form>
