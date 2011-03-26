{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
  {if $showpage.latest eq 'N'}
    {modurl modname='wikula' tag=$showpage.tag assign='showpageurl'}
    {modurl modname='wikula' tag=$showpage.tag func='revisions' assign='revisionsurl'}
    <div class="revisioninfo">
      <h4>{gt text='Revision [%s]' tag1=$showpage.id}</h4>
      <p>{gt text='This is a past revision of <a href="%2$s">%1$s</a> made by %3$s on <a class="datetime" href="%4$s">%5$s</a>' tag1=$showpage.tag tag2=$showpageurl tag3=$showpage.user tag4=$revisionsurl tag5=$time}</p>
      <form class="left" action="{modurl modname='wikula' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
        <input type="hidden" value="1" name="raw"/>
        <input type="submit" value="{gt text='Show Source'}"/>
      </form>
      <form action="{modurl modname='wikula' func='edit' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="{$showpage.id|safehtml}" name="previous" />
        <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
        <input type="submit" value="{gt text='Edit Revision'}"/>
      </form>
      <div class="clear"></div>
    </div>
  {/if}

  <div class="page">
	{if $modvars.Wikula.hidehistory neq true}
	  {* invokes the pagehistory directly *}
	  {*modapifunc modname='wikula' type='action' func='pageauthors' tag=$showpage.tag page=$showpage.page*}
	{/if}

    {* $body is the variable containing the stuff *}
    {$showpage.body|wakka}{*pnmodcallhooks:'wikula'*}
  </div>

  <div class="wiki_footer">
    <div style="text-align:left; padding:4px;">
      <form action="{textsearchlink}" method="post" enctype="application/x-www-form-urlencoded">
      <div>
        {if $canedit eq true}
        <a href="{modurl modname='wikula' func='edit' previous=$showpage.id|urlencode tag=$tag|urlencode}" title="{gt text='Edit page'}">{gt text='Edit page'}</a>
        <span class="text_separator">::</span>
        {/if}
        <a href="{modurl modname='wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
        <span class="text_separator">::</span>
        {gt text='Revisions of "%tag%" Feed' tag=$tag assign='altrssfeed'}
        <a href="{modurl modname='wikula' tag=$tag|urlencode time=$showpage.time|urlencode}" class="datetime">{$showpage.time|date_format}</a> <a href="{modurl modname='wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss'}" title="{$altrssfeed}">{img src='rss.png' alt=$altrssfeed modname='wikula'}</a>
        <span class="text_separator">::</span>
        {gt text='Owner'}: {$showpage.owner|userprofilelink}
        <span class="text_separator">::</span>
        {pnuserloggedin assign='islogged'}
        {if $islogged}
        <a href="{modurl modname='wikula' func='referrers' tag=$tag|urlencode}" title="{gt text='Referrers'}">{gt text='Referrers'}</a>
        <span class="text_separator">::</span>
        {/if}
        <label for="wikula_phrase">{gt text='Search for'}
        <input id="wikula_phrase" name="phrase" size="12" class="searchbox" />
        </label>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="clear"></div>
<div>
  {* the next code is to display any hooks (e.g. comments, ratings) *}
  {modurl modname='wikula' func='display' tag=$tag assign='returnurl'}
  {*modcallhooks hookobject='item' hookaction='display' hookid=$tag module='wikula' returnurl=$returnurl*}
</div>
