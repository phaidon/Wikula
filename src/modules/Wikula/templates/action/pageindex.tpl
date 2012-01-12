<div class="action_pageindex">

    <p class="z-informationmsg">
        {gt text='This is an alphabetical list of pages you can read on this server.'}
        {if $userownspages}
        {gt text='Items marked with a * indicate pages that you own.'}
        {/if}
    </p>

    
    {letterList pages=$pages}

</div>
