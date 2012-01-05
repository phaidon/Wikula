{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    <div class="page">

        <p class="z-clearfix"><a class="z-icon-es-rss z-floatright" href="{modurl modname='Wikula' type='user' func='RevisionsXML' tag=$tag theme='rss'}">{gt text='Show feed of these changes'}</a></p>

        {assign var='lastauthor' value=''}
        {assign var='lastedit' value=''}

        {foreach name='object' item='object' from=$objects}
        <div class="wikula_alignmiddle">
            {usergetidfromname uname=$object.EditedByUser assign='uid'}
            {useravatar uid=$uid}
            <strong>
                {if $smarty.foreach.object.first}
                {gt text='Last edit'}
                {assign var='lastauthor' value=$object.EditedByUser}
                {assign var='lastedit' value=$object.pageAtime|dateformat}
                {else}
                {gt text='Edit'}
                {/if}
                <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag time=$object.pageAtimeurl}" title="{$tag|safehtml} - {$object.pageAtime|dateformat|safehtml}">{$object.pageAtime|dateformat|safehtml}</a>
                {gt text='by %s' tag1=$object.EditedByUser|profilelinkbyuname}
            </strong>
            <span class="pagenote changenote">{if $object.note ne ''}[ {$object.note} ]{/if}</span>
        </div>

        {if $object.added neq ''}
        <p><strong>{gt text='Additions'}</strong></p>
        <div class="additions">{$object.newcontent|notifyfilters:'wikula.filter_hooks.body.filter'}</div>
        {/if}
        {if $object.deleted neq ''}
        <p><strong>{gt text='Deletions'}</strong></p>
        <div class="deletions">{$object.oldcontent|notifyfilters:'wikula.filter_hooks.body.filter'}</div>
        {/if}
        <hr />
        {/foreach}

        <div class="wikula_alignmiddle">
            {usergetidfromname uname=$oldest.user assign='uid'}
            {useravatar uid=$uid}
            <strong>
                {gt text='Oldest known version of this page was edited on'} <a href="{modurl modname='Wikula' type='user' func='main' tag=$tag time=$oldest.time|dateformat|@urlencode}" title="{$tag|safehtml} - {$oldest.time|dateformat|safehtml}">{$oldest.time|dateformat|safehtml}</a>
                {gt text='by'} {$oldest.user|profilelinkbyuname}
                <span style="color:#888;font-size:smaller;">{if $oldest.note ne ''}[ {$oldest.note|safehtml} ]{/if}</span>
            </strong>
        </div>
        {$oldest.body|notifyfilters:'wikula.filter_hooks.body.filter'}
    </div>

    <div class="wiki_footer">
        <div class="inforevision">
            {gt text='Last edit: %s' tag1=$lastedit|safehtml}<br />
            {gt text='Latest author: %s' tag1=$lastauthor|profilelinkbyuname}<br />
            {gt text='Owner: %s' tag1=$oldest.owner|profilelinkbyuname}
        </div>
    </div>

</div>