<div class="action_pageindex">
    <p class="z-informationmsg">{gt text='The following list shows those pages held in the Wiki that are not linked to on any other pages.'}</p>
    {if !empty($items)}
    <ul>
        {foreach from=$items item='item'}
        {if $item.tag}
        <li><a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="{$item.tag|safehtml}">{$item.tag|hyphen2space|safehtml}</a></li>
        {/if}
        {/foreach}
    </ul>
    {else}
    <ul>
        <li><em>{gt text='No orphaned pages! Good!'}</em></li>
    </ul>
    {/if}
</div>