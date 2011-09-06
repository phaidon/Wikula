<div id="Wikula">

{foreach from=$pages item="page"}
    <a href="{modurl modname='Wikula' type='user' func='main' tag=$page}">{$page}</a>
    <br />
{/foreach}

<br />
<a href="{modurl modname='Wikula' type='user' func='settings'}">{gt text='Settings'}</a>

</div>