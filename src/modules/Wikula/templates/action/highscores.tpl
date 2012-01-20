{if $items}
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text='Rank'}</th>
            <th>{gt text='User'}</th>
            <th>{gt text='Revision count'}</th>
            <th>{gt text='Page count'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$items key='user' item='item'}
        <tr class="{cycle values="z-odd,z-even"}">
            <td>{$item.i}</td>
            <td>{$user|profilelinkbyuname}</td>
            <td>{$item.revisions|safetext} ({$item.revisions/$total.revisions*100|formatnumber}%)</td>
            <td>{$item.pages|safetext} ({$item.pages/$total.pages*100|formatnumber}%)</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}