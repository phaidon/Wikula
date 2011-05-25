{if $items}
<h5>{gt text='Highscores'}</h5>
<table>
  <thead>
    <tr>
      <th>&nbsp;{gt text='Rank'}&nbsp;</th>
      <th>&nbsp;{gt text='User'}&nbsp;</th>
      <th>&nbsp;{gt text='Revision count'}&nbsp;</th>
      <th>&nbsp;{gt text='Percentage'}&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$items key='position' item='item'}
    <tr>
      <td>{$position+1}</td>
      <td>{$item.user}</td>
      <td style="text-align:right;">{$item.count}</td>
      <td style="text-align:right;">{$item.count/$total}%</td>
    </tr>
    {/foreach}
  </tbody>
</table>
{/if}
