<div id="wikulaheader">
  <div class="header">
    <h2>
        <a href="{modurl modname='Wikula' type='user' func='main'}">{wikiname}</a> &#187; 
        <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode}" title="{gt text='Show page'}">
            {$tag|safehtml}
        </a>
    </h2>

    <div>
        {edit tag=$tag}
        <a href="{modurl modname='Wikula' type='user' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
        <span class="text_separator">::</span>
        {userloggedin assign='islogged'}
        {if $islogged}
        <a href="{modurl modname='Wikula' type='user' func='backlinks' tag=$tag|urlencode}" title="{gt text='Backlinks'}">{gt text='Backlinks'}</a>
        {/if}
    </div>

    {insert name='getstatusmsg'}
  </div>
</div>

<br />