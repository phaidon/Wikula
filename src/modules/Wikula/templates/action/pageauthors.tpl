<div id="wiki_authorbox">
    <div class="wiki_authorboxelement z-clearfix">
        <h4>{gt text='Site creator'}:</h4>

        {usergetvar uid=$first_writer.uid name='_YOURAVATAR' assign='avatar'}

        {if $avatar eq '' OR $avatar eq 'blank.gif'}
        {img modname='Wikula' src='avatar_male.gif' width='50'}
        {else}
        <img src="images/avatar/{$avatar|safehtml}" width="50" />
        {/if}
        {$first_writer.uname|profilelinkbyuname}<br />
        {$first_writer.time|date_format}
    </div>

    <div class="wiki_authorboxelement">
        <h4>{gt text='Last authors'}:</h4>
        {foreach from=$history item='author'}
        <div class="z-clearfix">
            {usergetvar uid=$author.uid name='_YOURAVATAR' assign='avatar'}

            {if $avatar eq '' OR $avatar eq 'blank.gif'}
            {img modname='Wikula' src='avatar_male.gif' width='25'}
            {else}
            <img src="images/avatar/{$avatar|safehtml}" width="25" />
            {/if}
            {$author.uname|profilelinkbyuname}<br />
            {$author.time|date_format}
        </div>
        {/foreach}
    </div>

    <a href="{modurl modname='Wikula' func='history' tag=$tag|urlencode}" title="{gt text='Page history'}">{gt text='Page history'}</a>
</div>
