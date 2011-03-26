{* $Id: action/calendar.tpl 41 2008-10-09 18:29:16Z quan $ *}
<table cellpadding="2" cellspacing="1" class="calendar" summary="{$summary}">
<caption>{$monthYear}</caption>
<thead>
  <tr>
  {foreach key='key' item='aWeek' from=$aWeekdaysLong}
    <th scope="col" width="26" abbr="{$aWeekdaysLong.$key}">{$aWeekdaysShort.$key}</th>
  {/foreach}
  </tr>
</thead>
<tbody class="face">
{if $firstwday > 0}
  <tr>
{/if}

  {foreach item='fcell' from=$emptyfcells}
    <td>{$fcell}</td>
  {/foreach}
{foreach item='days' from=$monthcontent}
  {if $days eq 'start'}
    <tr>
  {elseif $days eq $wikkatoday}
    <td title="TODAY" class="currentday">{$days}</td>
  {elseif $days eq 'end'}
    </tr>
  {else}
    <td>{$days}</td>
  {/if}
{/foreach}
  {foreach item='lcell' from=$emptylcells}
    <td>{$lcell}</td>
  {/foreach}
  {if $wday < 6}
    </tr>
  {/if}
</tbody>
{if $hasActionParams eq false}
<tbody class="calnav">
  <tr>
    <td colspan="3" align="left" class="prevmonth"><a href="{$urlPrev}" title="{$titlePrev}">&lt;&lt;</a></td>
    <td align="center" class="curmonth"><a href="{$urlCur}" title="{$titleCur}">=</a></td>
    <td colspan="3" align="right" class="nextmonth"><a href="{$urlNext}" title="{$titleNext}">&gt;&gt;</a></td>
  </tr>
</tbody>
{/if}
</table>
