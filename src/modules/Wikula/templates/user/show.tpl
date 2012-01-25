{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    {if $latest eq 'N'}
    {modurl modname='Wikula' type='user' func='show' tag=$tag assign='showpageurl'}
    {modurl modname='Wikula' type='user' func='show' tag=$tag func='revisions' assign='revisionsurl'}
    <div class="revisioninfo z-clearfix">
        <h4>{gt text='Revision [%s]' tag1=$id}</h4>
        <p>
            {gt text='This is a past revision of <a href="%2$s">%1$s</a> made by %3$s on <a class="datetime" href="%4$s">%5$s</a>' tag1=$tag|safehtml tag2=$showpageurl|safehtml tag3=$user|safehtml tag4=$revisionsurl|safehtml tag5=$time|dateformat|safehtml}
        </p>
        <form class="z-floatleft" action="{modurl modname='Wikula' type='user' func='show' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <input type="hidden" value="{$id|safehtml}" name="rev"/>
                <input type="hidden" value="1" name="raw"/>
                <input class="z-button z-bt-small" type="submit" value="{gt text='Show Source'}"/>
            </div>
        </form>
        <form action="{modurl modname='Wikula' type='user' func='edit' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <input type="hidden" value="{$id|safehtml}" name="previous" />
                <input type="hidden" value="{$id|safehtml}" name="rev"/>
                <input class="z-button z-bt-small" type="submit" value="{gt text='Edit Revision'}"/>
            </div>
        </form>
    </div>
    {/if}

    <div class="page">
        {$body|notifyfilters:'wikula.filter_hooks.body.filter'}
    </div>

</div>

{notifydisplayhooks eventname='wikula.ui_hooks.bottom.display_view' id=$tag assign='hooks' caller="Wikula"}
{foreach from=$hooks key='provider_area' item='hook'}
{$hook}
{/foreach}