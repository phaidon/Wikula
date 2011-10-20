{include file='user/menu_category.tpl' tag=$tag}

<p class="z-informationmsg">
    {gt text='Pages that belong to this category:'}
</p>

<div id="wikula">
    <div class="page">

    {assign var='currentchar' value=''}
    {foreach name='mypages' item='letter' from=$pagelist key='firstchar'}
    {if $currentchar neq $firstchar}
    {assign var='currentchar' value=$firstchar}
    <strong>{$firstchar}</strong><br />
    {/if}
    {foreach name='mypagespage' item='page' from=$letter}
    &nbsp;&nbsp;&nbsp;
    <a href="{modurl modname='Wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
    {if $smarty.foreach.mypages.last ne true}
    <br />
    {/if}
    {/foreach}
    {if $smarty.foreach.mypages.last ne true}
    <br />
    {/if}
    {/foreach}

    </div>
</div>
