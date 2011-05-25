{if $action_cc.pages}
  {if $action_cc.compact eq 0}
    <table{if $action_cc.full} width="100%"{/if}>
      {if $action_cc.notitle eq 0}
      <thead>
        <tr>
          <th{if $action_cc.col > 1} colspan="{$action_cc.col}"{/if}>
            {gt text='Pages that belong to this category '} <a href="{modurl modname='wikula' tag=$action_cc.tag|urlencode}" title="{$action_cc.tag}">{$action_cc.tag}</a> ({$action_cc.total})
          </th>
        </tr>
      </thead>
      <tbody>
      {/if}
        <tr>
        {counter name='categorycategory' start=0 print=false assign='action_cc_count'}
        {foreach from=$action_cc.pages item='action_cc_page' name='action_cc_pages'}
          <td>
            <a href="{modurl modname='wikula' tag=$action_cc_page.page_tag|urlencode}" title="{$action_cc_page.page_tag}">{$action_cc_page.page_tag}</a>
          </td>
          {counter name='categorycategory' print=false}
        {* row break check *}
        {if $action_cc_count MOD $action_cc.col eq 0 AND $smarty.foreach.action_cc_pages.last neq true}
        </tr>
        <tr>
        {/if}
        {/foreach}
        {*if $action_cc.endcells > 1*}
        {if $action_cc_count < $action_cc.col}
          {*<td colspan="{$endcells}">&nbsp;</td>*}
          {math equation='x-y' x=$action_cc.col y=$action_cc_count assign='action_cc_endcell'}
          <td{if $action_cc_endcell > 1} colspan="{$action_cc_endcell}"{/if}>&nbsp;</td>
        {/if}
        </tr>
      {if $action_cc.notitle eq 0}
      </tbody>
      {/if}
    </table>
  {else}
    {if $action_cc.notitle eq 0}
    <h5>{gt text='Pages that belong to this category '} <a href="{modurl modname='wikula' tag=$action_cc.tag|urlencode}" title="{$action_cc.tag}">{$action_cc.tag}</a></h5>
    {/if}
    <div class="categorycategory">
      <ul>
        {foreach from=$action_cc.pages item='action_cc_page'}
        <li><a href="{modurl modname='wikula' tag=$action_cc_page.page_tag|urlencode}" title="{$action_cc_page.page_tag}">{$action_cc_page.page_tag}</a></li>
        {/foreach}
      </ul>
    </div>
  {/if}
{else}
  <p>{gt text='No pages found in this category!'}</p>
{/if}
