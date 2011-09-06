<h2>{gt text='Useful pages'}</h2>

<ul>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='FormattingRules'}">{gt text='FormattingRules'}</a>:
        {gt text='learn how to format the contents in this wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='SandBox'}">{gt text='SandBox'}</a>:
        {gt text='play with your formatting skills'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='PageIndex'}">{gt text='PageIndex'}</a>:
        {gt text='index of the available pages on the wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='WantedPages'}">{gt text='WantedPages'}</a>:
    {gt text='check out the pages pending for creation'}</li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='OrphanedPages'}">{gt text='OrphanedPages'}</a>:
        {gt text='list of orphaned pages'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='Search'}">{gt text='Search'}</a>:
        {gt text='search something of your interest in the wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='TextSearchExpanded'}">{gt text='TexSearchExpanded'}</a>:
        {gt text="fine grained search if you haven't found anything in the normal search"}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='WikiCategory'}">{gt text='WikiCategory'}</a>:
        {gt text='learn how works the categorization system of this wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='CategoryWiki'}">{gt text='CategoryWiki'}</a>:
        {gt text='list of pages related to the Wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='RecentChanges'}">{gt text='RecentChanges'}</a>:
        {gt text='check which pages that were changed recently'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='HighScores'}">{gt text='HighScores'}</a>:
        {gt text='check who had contributed more to the wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='OwnedPages'}">{gt text='OwnedPages'}</a>:
        {gt text='check out how many pages you own on the wiki'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='MyChanges'}">{gt text='MyChanges'}</a>:
        {gt text='list of changes that you have done'}
    </li>
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag='MyPages'}">{gt text='MyPages'}</a>:
        {gt text='list of pages that you own on this wiki'}
    </li>
</ul>

{modurl modname='Wikula' type='user' func='main' tag='PageIndex' assign='pageindexurl'}
<p>
    {gt text='You will find more useful pages in the <a href="%s">Page index</a>.' tag1=$pageindexurl|safetext}
</p>
