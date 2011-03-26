{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
  <div class="page">
    {modurl modname='wikula' tag=$tag|urlencode assign='pageurl'}
    <h3>{gt text='Pages linking to <a href="%1$s">%2$s</a>' tag2=$tag|safehtml tag1=$pageurl}</h3>
    {foreach from=$pages item='page'}
      <a href="{modurl modname='wikula' tag=$page|urlencode}" title="{$page|safehtml}">{$page|safehtml}</a><br />
    {foreachelse}
      <em class="wikula_error">{gt text='There are no backlinks to this page'}</em>
    {/foreach}
    <br /><br />
  </div>
  <div class="wiki_footer">
    <div style="text-align:left; padding:4px;">
      <form action="{textsearchlink}" method="post" enctype="application/x-www-form-urlencoded">
      <div>
        <a href="{modurl modname='wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
        <span class="text_separator">::</span>
        {gt text='Revisions of "%s" Feed' tag1=$tag assign='altrssfeed'}
        <a href="{modurl modname='wikula' tag=$tag|urlencode time=$backpage.time|urlencode}" class="datetime">{$backpage.time|safehtml}</a> <a href="{modurl modname='wikula' func='RevisionsXML' tag=$tag|urlencode theme='rss'}" title="{$altrssfeed}">{img src='rss.png' alt=$altrssfeed modname='wikula'}</a>
        <span class="text_separator">::</span>
        {gt text='Owner'}: {$backpage.owner|userprofilelink}
        <span class="text_separator">::</span>
        <label for="wikula_phrase">{gt text='Search for'}
        <input id="wikula_phrase" name="phrase" size="12" class="searchbox" />
        </label>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="clear">&nbsp;</div>
{*include file='wikula_user_footer.tpl'*}
