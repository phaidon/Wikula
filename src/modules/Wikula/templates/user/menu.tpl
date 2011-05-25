<div id="wikulaheader">
  <div class="header">
    <h2>
        <a href="{modurl modname='Wikula' type='user' func='main'>{wikiname}</a> &#187; 
        <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag|urlencode}">{$tag|safehtml}</a>
    </h2>    
	
    <div class="pn-menu">
    {if $tag neq $modvars.Wikula.root_page}
      <a href="{modurl modname='Wikula'}" title="{$modvars.Wikula.root_page}">{$modvars.Wikula.root_page}</a>
    {else}
      <span>{$modvars.Wikula.root_page}</span>
    {/if}

    <span class="text_separator">::</span>

    {if $tag neq $smarty.const.CategoryCategory}
      <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.CategoryCategory}" title="">{gt text='Categories'}</a>
    {else}
      <span>{gt text='Categories'}</span>
    {/if}

    <span class="text_separator">::</span>

    {if $tag neq $smarty.const.PageIndex}
      <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.PageIndex}" title="">{gt text='Page index'}</a>
    {else}
      <span>{gt text='Page index'}</span>
    {/if}

    <span class="text_separator">::</span>

    {if $tag neq $smarty.const.TextSearch}
      <a href="{modurl modname='Wikula' type='user' __tag='Search'}" title="">{gt text='Search'}</a>
    {else}
      <span>{gt text='Search'}</span>
    {/if}

    <span class="text_separator">::</span>

    {if $tag neq $smarty.const.WikiHelp}
      <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.WikiHelp}" title="">{gt text='Help'}</a>
    {else}
      <span>{gt text='Help'}</span>
    {/if}

    <span class="text_separator">::</span>

    {pnuserloggedin assign='islogged'}
    {if $islogged eq false}
      <a href="{modurl modname='Users' func='loginscreen'}" title="">{gt text='Log in'}</a>
      <span class="text_separator">::</span>
    {/if}

    {if $tag neq $smarty.const.RecentChanges}
      <a href="{modurl modname='Wikula' type='user' tag=$smarty.const.RecentChanges}" title="">{gt text='Recent changes'}</a>
    {else}
      <span>{gt text='Recent changes'}</span>
    {/if}
    <a href="{modurl modname='Wikula' func='recentchangesxml' theme='rss'}" title="{gt text='Recent changes Feed'}">{img modname='Wikula' src='rss.png' __title='Recent changes Feed'  __alt='RSS'}</a>
    </div>

    {insert name='getstatusmsg'}
  </div>
</div>
