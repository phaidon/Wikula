{* $Id: action/textsearchexpanded.tpl 41 2008-10-09 18:29:16Z quan $ *}

<form action="{modurl modname='wikula' type='user' func='main' tag=$TextSearchExpandedTag}" method="post" enctype="application/x-www-form-urlencoded">
<div>
  <label for="action/phrase">{gt text='Search for'}</label>
  <input id="action/phrase" class="searchbox" name="phrase" size="35" value="{$phrase}" />
  <input type="submit" value="{gt text='Search'}" /></label>
</div>
</form>
<br />

{if !$notfound AND !empty($phrase)}
<p>{gt text='Search result: <strong>%matches% matches</strong> for <strong>%phrase%</strong>' matches=$resultcount phrase=$phrase}</p>
<ol>
  {foreach from=$results item='result'}
  <li>
    <a href="{modurl modname='wikula' tag=$result.page_tag}" title="{$result.page_tag}">{$result.page_tag}</a> &mdash; {$result.page_time}
    {if !empty($result.matchtext)}
    <blockquote>... {$result.matchtext} ...</blockquote>
    {/if}
  </li>
  {/foreach}
</ol>

{elseif $notfound}

<p>
  {gt text='Search string not found'}.
  {if $phrase neq ''}
  <br />
  <a href="{modurl modname='wikula' type='user' func='edit' tag=$phrase|capitalize:true|replace:' ':''|formatpermalink}">
    {gt text='Click here to create a new page named "%tag%"' tag=$phrase|safehtml}
  </a>.
  {/if}
</p>
{/if}

<hr />
{gt text='<strong>Search Tips:</strong><br /><br /><div class="indent">apple banana</div>Find pages that contain at least one of the two words. <br /><br /><div class="indent">+apple +juice</div>Find pages that contain both words. <br /><br /><div class="indent">+apple -macintosh</div>Find pages that contain the word <q>apple</q> but not <q>macintosh</q>. <br /><br /><div class="indent">apple*</div>Find pages that contain words such as apple, apples, applesauce, or applet. <br /><br /><div class="indent">&quot;some words&quot;</div>Find pages that contain the exact phrase <q>some words</q> (for example, pages that contain <q>some words of wisdom</q> <br />but not <q>some noise words</q>). '}
