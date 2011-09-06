<div id="wiki_authorbox">
    <div class="wiki_authorboxelement z-clearfix">
        <h4>{gt text='Site creator'}:</h4>
        {useravatar uid=$first_writer.uid}
        {$first_writer.uname|profilelinkbyuname}<br />
        {$first_writer.time|date_format}
    </div>

    <div class="wiki_authorboxelement">
        <h4>{gt text='Last authors'}:</h4>
        {foreach from=$history item='author'}
        <div class="z-clearfix">
            {useravatar uid=$author.uid}
            {$author.uname|profilelinkbyuname}<br />
            {$author.time|date_format}
        </div>
        {/foreach}
    </div>

    <a href="{modurl modname='Wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
</div>
