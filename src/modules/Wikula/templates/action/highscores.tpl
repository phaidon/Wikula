{* $Id: action/highscores.tpl 41 2008-10-09 18:29:16Z quan $ *}

{if $items}
<h5>{gt text='Highscores'}</h5>
<table{if $hsfull} width="100%"{/if}>
  <thead>
    <tr>
      <th>&nbsp;{gt text='Rank'}&nbsp;</th>
      <th>&nbsp;{gt text='User'}&nbsp;</th>
      <th>&nbsp;{gt text='Page Count'}&nbsp;</th>
      <th>&nbsp;{gt text='Percentage'}&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$items item='item'}
    <tr>
      <td>{$item.position}</td>
      <td>{$item.uname}</td>
      <td style="text-align:right;">{$item.count}</td>
      <td style="text-align:right;">{$item.percent}%</td>
    </tr>
    {/foreach}
  </tbody>
</table>
{/if}
