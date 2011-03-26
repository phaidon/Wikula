{* $Id: action/wantedpages.tpl 41 2008-10-09 18:29:16Z quan $ *}

{if !empty($items)}
<div class="action_wantedpages">

  {* linked pages *}
  {if !empty($linkingto)}
  {modurl modname='wikula' tag=$linkingto|urlencode assign='url'}
  <h5>{gt text='Pages linking to: %s' tag1=$url}:</h5>
  <ul>
    {foreach from=$items item='item'}
    <li><a href="{modurl modname='wikula' tag=$item|urlencode}">{$item|safehtml}</a></li>
    {/foreach}
  </ul>

  {* wanted pages *}
  {else}
  <h5>{gt text='Wanted Pages'} ({$items|@count}):</h5>
  <table>
    <thead>
     <tr>
       <th>{gt text='Source page'}</th>
       <th>&rArr;</th>
       <th>{gt text='Targetted inexistent page'}</th>
     </tr>
    </thead>
    <tbody>
      {foreach from=$items item='item'}
      <tr>
        <td><a href="{modurl modname='wikula' func='main' tag=$item.from_tag|urlencode}" title="{$item.from_tag|safehtml}">{$item.from_tag|safehtml}</a></td>
        <td>&rArr;</td>
        <td>{pnmodapifunc modname='wikula' type='user' func='Link' tag=$item.to_tag}</td>
      </tr>
      {/foreach}
    </tbody>
  </table>
  {/if}

</div>
{/if}
