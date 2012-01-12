<div class="action_pageindex">
    <p class="z-informationmsg">
        {gt text='The following list shows those pages held in the Wiki that are not linked to on any other pages.'}
    </p>
    
    
    
    {if !empty($items)}
        {letterList pages=$items}
    {else}
        <ul>
            <li>
                <em>{gt text='No orphaned pages! Good!'}</em>
            </li>
        </ul>
    {/if}
</div>