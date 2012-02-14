<div id="wikula">

    <form class="z-form" action="{modurl modname='Wikula' type='user' func='main' __tag='Search'}" method="post" enctype="application/x-www-form-urlencoded">
        <fieldset>
            <label for="phrase">{gt text='Search for'}</label>
            <input id="phrase" name="phrase" size="35" value="{$phrase}" type="tex" />
            {if $modvars.Wikula.fulltextsearch}
                <label for="fulltextsearch">{gt text='Full text search'}</label>
                <input id="fulltextsearch" name="fulltextsearch" value="1" {if $fulltextsearch}checked="checked"{/if} type="checkbox" />
            {/if}
            <span class="z-buttons">
            <input class="z-bt-ok z-bt-small" type="submit" value="{gt text='Search'}" />
            </span>
            <span class="z-buttons">
                <a class="z-bt-small" id="defwindow" href="#defwindow_content" title="{gt text='Search Tips'}">
                    {img src='help.png' modname='core' set='icons/extrasmall'  __alt='Search Tips' __title='Search Tips'}
                </a>
            </span>
            <div id="defwindow_content" style="display: none;">
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
            </div>
            <script type="text/javascript">
                var defwindow = new Zikula.UI.Window($('defwindow'));
            </script>
        </fieldset>
    </form>              

    {if !$notfound AND !empty($phrase)}
        <p>
            {gt text='Search result: <strong>%1$s matches</strong> for <strong>%2$s</strong>' tag1=$resultcount|safehtml tag2=$phrase|safehtml}
        </p>

        <ol>
            {foreach from=$results item='result'}
                <li>
                    <a href="{modurl modname='Wikula' type='user' func='main' tag=$result.tag|safehtml}" title="{$result.tag|safehtml}">
                        {$result.tag|safehtml}
                    </a><br />
                    {$result.body|notifyfilters:'wikula.filter_hooks.body.filter'|teaser:$phrase}
                </li>
            {/foreach}
        </ol>
        <br />
    {elseif $notfound}
        <p>
            {gt text='Search string not found.'}
            {if $phrase neq ''}
                <br />
                <a href="{modurl modname='Wikula' type='user' func='edit' tag=$phrase|capitalize:true|replace:' ':''|formatpermalink}">
                    {gt text='Click here to create a new page named "%s"' tag1=$phrase|safehtml}
                </a>.
            {/if}
        </p>
    {/if}

</div>