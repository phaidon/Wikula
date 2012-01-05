<ul class="wikula_block_menu">
    {foreach from=$pages item="page"}
    <li><a href="{modurl modname='Wikula' type='user' func='main' tag=$page|safehtml}">{$page|safehtml}</a></li>
    {/foreach}
    {if $modvars.Wikula.subscription}
    <li><a href="{modurl modname='Wikula' type='user' func='settings'}">{gt text='Settings'}</a></li>
    {/if}
</ul>
