{* $Id: action/textsearch.tpl 83 2008-12-17 04:04:58Z mateo $ *}

<form action="{textsearchlink}" method="post" enctype="application/x-www-form-urlencoded">
<div>
  <label for="action/phrase">{gt text='Search for'}</label>
  <input id="action/phrase" class="searchbox" name="phrase" size="35" value="{$phrase}" />
  <input type="submit" value="{gt text='Search'}" /></label>
</div>
</form>
<br />

{if !$notfound AND !empty($phrase)}
<p>{gt text='Search result: <strong>%1$s matches</strong> for <strong>%2$s</strong>' tag1=$resultcount tag2=$phrase}</p>
<ol>
  {foreach from=$results item='result'}
  <li><a href="{modurl modname='wikula' tag=$result.page_tag}" title="{$result.page_tag}">{$result.page_tag}</a></li>
  {/foreach}
</ol>
<br />
{modurl modname='wikula' tag=$TextSearchExpandedTag phrase=$phrase assign='searchurl'}
<p>{gt text='Not sure which page to choose?<br />Try the <a href="%s" title="Expanded Text Search">Expanded Text Search</a> which shows surrounding text.' tag1=$searchurl}</p>

{elseif $notfound}

<p>
  {gt text='Search string not found'}.
  {if $phrase neq ''}
  <br />
  <a href="{modurl modname='wikula' type='user' func='edit' tag=$phrase|capitalize:true|replace:' ':''|formatpermalink}">
    {gt text='Click here to create a new page named "%s"' tag1=$phrase|safehtml}
  </a>.
  {/if}
</p>
{/if}

<hr />
{gt text='<strong>Search Tips:</strong><br /><br /><div class="indent">apple banana</div>Find pages that contain at least one of the two words. <br /><br /><div class="indent">+apple +juice</div>Find pages that contain both words. <br /><br /><div class="indent">+apple -macintosh</div>Find pages that contain the word <q>apple</q> but not <q>macintosh</q>. <br /><br /><div class="indent">apple*</div>Find pages that contain words such as apple, apples, applesauce, or applet. <br /><br /><div class="indent">&quot;some words&quot;</div>Find pages that contain the exact phrase <q>some words</q> (for example, pages that contain <q>some words of wisdom</q> <br />but not <q>some noise words</q>). '}
