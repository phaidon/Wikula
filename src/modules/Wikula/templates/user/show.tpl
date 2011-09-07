{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    {if $showpage.latest eq 'N'}
    {modurl modname='Wikula' tag=$showpage.tag assign='showpageurl'}
    {modurl modname='Wikula' tag=$showpage.tag func='revisions' assign='revisionsurl'}
    <div class="revisioninfo z-clearfix">
        <h4>{gt text='Revision [%s]' tag1=$showpage.id}</h4>
        <p>{gt text='This is a past revision of <a href="%2$s">%1$s</a> made by %3$s on <a class="datetime" href="%4$s">%5$s</a>' tag1=$showpage.tag|safehtml tag2=$showpageurl|safehtml tag3=$showpage.user|safehtml tag4=$revisionsurl|safehtml tag5=$time|safehtml}</p>
        <form class="z-floatleft" action="{modurl modname='Wikula' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
                <input type="hidden" value="1" name="raw"/>
                <input class="z-button z-bt-small" type="submit" value="{gt text='Show Source'}"/>
            </div>
        </form>
        <form action="{modurl modname='Wikula' func='edit' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <input type="hidden" value="{$showpage.id|safehtml}" name="previous" />
                <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
                <input class="z-button z-bt-small" type="submit" value="{gt text='Edit Revision'}"/>
            </div>
        </form>
    </div>
    {/if}

    <div class="page">
        {$showpage.body|notifyfilters:'wikula.filter_hooks.body.filter'}
    </div>

</div>

{notifydisplayhooks eventname='wikula.ui_hooks.bottom.display_view' id=$showpage.tag assign='hooks' caller="Wikula"}
{foreach from=$hooks key='provider_area' item='hook'}
{$hook}
{/foreach}