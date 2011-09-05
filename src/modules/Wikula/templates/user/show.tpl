{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
  {if $showpage.latest eq 'N'}
    {modurl modname='wikula' tag=$showpage.tag assign='showpageurl'}
    {modurl modname='wikula' tag=$showpage.tag func='revisions' assign='revisionsurl'}
    <div class="revisioninfo">
      <h4>{gt text='Revision [%s]' tag1=$showpage.id}</h4>
      <p>{gt text='This is a past revision of <a href="%2$s">%1$s</a> made by %3$s on <a class="datetime" href="%4$s">%5$s</a>' tag1=$showpage.tag tag2=$showpageurl tag3=$showpage.user tag4=$revisionsurl tag5=$time}</p>
      <form class="left" action="{modurl modname='wikula' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
        <input type="hidden" value="1" name="raw"/>
        <input type="submit" value="{gt text='Show Source'}"/>
      </form>
      <form action="{modurl modname='wikula' func='edit' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
        <input type="hidden" value="{$showpage.id|safehtml}" name="previous" />
        <input type="hidden" value="{$showpage.time|safehtml}" name="time"/>
        <input type="submit" value="{gt text='Edit Revision'}"/>
      </form>
      <div class="clear"></div>
    </div>
  {/if}

<div id="wikula">

  <div class="page" style="text-align:left">
    {$showpage.body|notifyfilters:'wikula.filter_hooks.body.filter'}
  </div>

</div>


{notifydisplayhooks eventname='wikula.ui_hooks.bottom.display_view' id=$showpage.tag assign='hooks' caller="Wikula"}
{foreach from=$hooks key='provider_area' item='hook'}
{$hook}
{/foreach}
