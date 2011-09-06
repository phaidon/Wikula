<div class="action_mypages">

    <p class="z-informationmsg">{gt text='This is the list of pages you own. (%1$s on %2$s)' tag1=$count tag2=$total}</p>

    {if $pagecount eq 0}
    <em>{gt text='This is a list of pages you'}</em>
    {/if}

    {if $pagelist}
    {assign var='currentchar' value=''}

    {foreach name='mypages' item='letter' from=$pagelist key='firstchar'}
    {if $currentchar neq $firstchar}
    {assign var='currentchar' value=$firstchar}
    <strong>{$firstchar}</strong><br />
    {/if}
    {foreach name='mypagespage' item='page' from=$letter}
    &nbsp;&nbsp;&nbsp;
    <a href="{modurl modname='wikula' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a>
    {if $smarty.foreach.mypages.last ne true}
    <br />
    {/if}
    {/foreach}
    {if $smarty.foreach.mypages.last ne true}
    <br />
    {/if}
    {/foreach}

    {else}
    <em>{gt text="You don't own any pages"}</em>
    {/if}

</div>
