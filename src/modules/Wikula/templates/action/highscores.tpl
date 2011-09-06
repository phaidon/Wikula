{if $items}
<h5>{gt text='Highscores'}</h5>
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text='Rank'}</th>
            <th>{gt text='User'}</th>
            <th>{gt text='Revision count'}</th>
            <th>{gt text='Percentage'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$items key='position' item='item'}
        <tr class="{cycle values="z-odd,z-even"}">
            <td>{$position+1}</td>
            <td>{$item.user|profilelinkbyuname}</td>
            <td>{$item.count|safetext}</td>
            <td>{$item.count/$total|formatnumber}%</td>
        </tr>
        {/foreach}
    </tbody>
</table>
{/if}