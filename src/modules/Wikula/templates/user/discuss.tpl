{include file='user/menu.tpl' tag=$tag}

{notifydisplayhooks eventname='wikula.ui_hooks.discuss.display_view' id=$tag assign='hooks' caller="Wikula"}
{foreach from=$hooks key='provider_area' item='hook'}
    {$hook}
{/foreach}