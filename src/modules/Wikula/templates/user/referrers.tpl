{include file='user/menu.tpl' tag=$tag}

{checkpermission realm='0' component='Wikula::' instance ='.*'         level='ACCESS_ADMIN' assign='accessadmin'}
{checkpermission realm='0' component='Wikula::' instance ="page::$tag" level='ACCESS_ADMIN' assign='accessedit'}
<div id="wikula">
  <div class="page">
    <h4 id="hn_result">{gt text='Filtered result'}: {$total} {gt text='Referrers to'} {$tag}</h4>
    <div class="refmenu">
      <ul class="menu">
        <li><a href="{modurl modname='wikula' func='referrers' tag=$tag|urlencode}" title="{gt text='Referrers to'} {$tag}">{gt text='Referrers to'} {$tag}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' sites=1 tag=$tag|urlencode}" title="{gt text='Domains linking to %thispage%' thispage=$tag}">{gt text='Domains linking to %s' tag1=$tag}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' global=1 tag=$tag|urlencode}" title="{gt text='Referrers to this wiki'}">{gt text='Referrers to this wiki'}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' sites=1 global=1 tag=$tag|urlencode}" title="{gt text='Domains linking to this wiki'}">{gt text='Domains linking to this wiki'}</a></li>
        <!--<li><a href="http://wikkawiki.org/HomePage/review_blacklist">Blacklisted sites</a></li>-->
      </ul>
    </div>
    <br class="clear" /><br />
    <form action="{modurl modname='wikula' type='user' func='Referrers' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded" id="form_refform">
    <div>
      <input type="hidden" name="global" value="0" />
      <input type="hidden" name="sites" value="0" />
      <fieldset>
        <legend>{gt text='Filter view'}:</legend>
        <label for="qo" class="mainlabel">URL:</label>
        <select name="qo" id="qo" title="Select search option">
          <option value="1" selected="selected">{gt text='containing'}</option>
          <option value="0">{gt text='not containing'}</option>
        </select>
        <label for="q">{gt text='string'}</label>
        <input type ="text" name="q" id="q" title="Enter a search string" size="20" maxlength="50" value="{$q}" /><br />
        <label for="ho" class="mainlabel">{gt text='Filter'}:</label>
        <select name="ho" id="ho" title="Select filter option">
          <option value="1" selected="selected">{gt text='at least'}</option>
          <option value="0">{gt text='no more than'}</option>
        </select>
        <input type="text" name="h" id="h" title="Enter number of hits" size="5" maxlength="5" value="{$h}" />
        <label for="h">{gt text='hits'}</label><br />
        <label for="days" class="mainlabel">{gt text='Period'}:</label>
        <select name="days" id="days" title="Select period in days">
          <option value="1" selected="selected">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
          <option value="7">7</option>
          <option value="14">14</option>
          <option value="21">21</option>
          <option value="28">28</option>
          <option value="30">30</option>
        </select>
        <label for="h">{gt text='days'}</label>
      </fieldset>
      <input type="submit" name="submit" value="Show referrers" accesskey="r" />
    </div>
    </form>
    <br class="clear" />
    <table id="reflist" summary="Filtered list of referrers, with hits, sorted by number of hits">
    <thead>
      <tr>
        <th class="hits" scope="col">{gt text='Hits'}</th>
        <th class="refs" scope="col">{gt text='Referrers'}</th>
      </tr>
    </thead>
    {if $referrers}<tbody>{/if}
    {foreach item='referrer' from=$referrers}
      <tr>
        <td class="hits">{$referrer.num}</td>
        <td class="refs">{if $sites neq 1}<a href="{$referrer.referrer}" title="{$referrer.referrer}">{/if}{$referrer.referrer}{if $sites neq 1}</a>{/if}</td>
      </tr>
    {/foreach}
    </tbody>
    </table>
    {if $accessadmin}
    <div>
      <form action="{modurl modname='wikula' type='admin' func='ClearReferrers' tag=$tag}" method="post" enctype="application/x-www-form-urlencoded" id="form_clearrefform">
      <div>
        <input type="hidden" name="global" value="{$global}" />
        <input type="submit" value="Clear Referers" />
      </div>
      </form>
    </div>
    {/if}
    <div class="refmenu">
      <ul class="menu">
        <li><a href="{modurl modname='wikula' func='referrers' tag=$tag|urlencode}" title="{gt text='Referrers to'} {$tag}">{gt text='Referrers to'} {$tag}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' sites=1 tag=$tag|urlencode}" title="{gt text='Domains linking to %thispage%' thispage=$tag}">{gt text='Domains linking to %thispage%' thispage=$tag}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' global=1 tag=$tag|urlencode}" title="{gt text='Referrers to this wiki'}">{gt text='Referrers to this wiki'}</a></li>
        <li><a href="{modurl modname='wikula' func='referrers' sites=1 global=1 tag=$tag|urlencode}" title="{gt text='Domains linking to this wiki'}">{gt text='Domains linking to this wiki'}</a></li>
        <!--<li><a href="http://wikkawiki.org/HomePage/review_blacklist">Blacklisted sites</a></li>-->
      </ul>
    </div>
    <br class="clear" />
  </div>

  <div class="wiki_footer">
    <div style="text-align:left; padding:4px;">
      <form action="{textsearchlink}" method="post" enctype="application/x-www-form-urlencoded">
      <div>
        {if $accessedit}
        <a href="{modurl modname='wikula' func='edit' previous=$page.id|urlencode tag=$tag|urlencode}" title="{gt text='Edit page'}">{gt text='Edit page'}</a>
        <span class="text_separator">::</span>
        {/if}
        <a href="{modurl modname='wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
        <span class="text_separator">::</span>
        {gt text='Revisions of "%s" Feed' tag1=$tag assign='altrssfeed'}
        <a href="{modurl modname='wikula' tag=$tag|urlencode time=$page.time|urlencode}" class="datetime">{$page.time|safehtml}</a> <a href="{modurl modname='wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss'}" title="{$altrssfeed}">{img src='rss.png' alt=$altrssfeed modname='wikula'}</a>
        <span class="text_separator">::</span>
        {gt text='Owner'}: {$page.owner|profilelinkbyuname}
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
{*include file='wikula_user_footer.tpl'*}
<div class="clear">&nbsp;</div>
