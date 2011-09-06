<form class="z-form" action="{modurl modname='wikula' type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">
    <div>
        <fieldset>
            <label for="action_phrase">{gt text='Search for'}</label>
            <input id="action_phrase" class="searchbox" name="phrase" size="35" value="{$phrase}" />
            <span class="z-buttons"><input class="z-bt-ok z-bt-small" type="submit" value="{gt text='Search'}" /></span>
        </fieldset>
    </div>
</form>

{if !$notfound AND !empty($phrase)}
<p>{gt text='Search result: <strong>%1$s matches</strong> for <strong>%2$s</strong>' tag1=$resultcount|safehtml tag2=$phrase|safehtml}</p>
<ol>
    {foreach from=$results item='result'}
    <li>
        <a href="{modurl modname='Wikula' type='user' func='main' tag=$result.page_tag|safehtml}" title="{$result.page_tag|safehtml}">{$result.page_tag|safehtml}</a>
    </li>
    {/foreach}
</ol>
<br />
{modurl modname='Wikula' type='user' func='main' tag=$TextSearchExpandedTag phrase=$phrase assign='searchurl'}
<p>{gt text='Not sure which page to choose?<br />Try the <a href="%s" title="Expanded Text Search">Expanded Text Search</a> which shows surrounding text.' tag1=$searchurl|safehtml}</p>

{elseif $notfound}

<p>
    {gt text='Search string not found'}.
    {if $phrase neq ''}
    <br />
    <a href="{modurl modname='Wikula' type='user' func='edit' tag=$phrase|capitalize:true|replace:' ':''|formatpermalink}">{gt text='Click here to create a new page named "%s"' tag1=$phrase|safehtml}</a>.
    {/if}
</p>
{/if}

<h5>{gt text='Search Tips:'}</h5>
<dl>
    <dt>{gt text='apple banana'}</dt>
    <dd>{gt text='Find pages that contain at least one of the two words.'}</dd>
    <dt>{gt text='+apple +juice'}</dt>
    <dd>{gt text='Find pages that contain both words.'}</dd>
    <dt>{gt text='+apple -macintosh'}</dt>
    <dd>{gt text='Find pages that contain the word <q>apple</q> but not <q>macintosh</q>.'}</dd>
    <dt>{gt text='apple*'}</dt>
    <dd>{gt text='Find pages that contain words such as apple, apples, applesauce, or applet.'}</dd>
    <dt>{gt text='&quot;some words&quot;'}</dt>
    <dd>{gt text='Find pages that contain the exact phrase <q>some words</q> (for example, pages that contain <q>some words of wisdom</q> but not <q>some noise words</q>). '}</dd>
</dl>
