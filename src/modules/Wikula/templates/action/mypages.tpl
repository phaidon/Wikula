<div class="action_mypages">

    <p class="z-informationmsg">{gt text='This is the list of pages you own. (%1$s on %2$s)' tag1=$count tag2=$total}</p>

    {if $pagecount eq 0}
    <em>{gt text='This is a list of pages you'}</em>
    {/if}

    
    {letterList pages=$pages}
    

</div>
