{gt text='Page Administration' assign=templatetitle}
{gt text='view' assign=templateicon}
{include file='admin/header.tpl'}


<div id="wikkaadmin">
  <form action="{modurl modname='Wikula' type='admin' func='pages'}" class="z-form" method="post" enctype="application/x-www-form-urlencoded">
    <fieldset><legend>{gt text='Filter view:'}</legend>
      <div style="float:right">
      <label for="wikula_itemsperpage">{gt text='Show'}</label>
      {html_options id="wikula_itemsperpage" name="itemsperpage" values=$pageroptions output=$pageroptions selected=$itemsperpage}
      <label for="wikula_itemsperpage">{gt text='Items per page'}</label>
      <input type="submit" value="{gt text='Apply'}" />
      </div>

      <label for="wikula_q">{gt text='Search page:'}</label>
      <input type="text" id="wikula_q" name="q" title="{gt text='Enter search string:'}" size="20" maxlength="50" value="" />
      <input type="submit" value="{gt text='Submit'}" />

      <hr />
      {gt text='Records'} ({$total}): {$startnum}-{math equation='x+y' x=$startnum y=$itemcount-1} 
      ({gt text='Sorted by:'} <em>{$sort|default:'time'}, {$order|default:'DESC'}</em>)
    </fieldset>
  </form>

  <table style="width:100%;" summary="List of pages on this server">
  <thead>
  <tr>
      <th>&nbsp;</th>
      {if $order eq 'ASC'}
        {assign var='neworder' value='DESC'}
      {else}
        {assign var='neworder' value='ASC'}
      {/if}
      <th><a href="{modurl modname='Wikula' type='admin' func='pages' sort='tag' order=$neworder}" title="Sort by page name">{gt text='Page name'}</a></th>

      <th><a href="{modurl modname='Wikula' type='admin' func='pages' sort='owner' order=$neworder}" title="Sort by page owner">{gt text='Owner'}</a></th>
      <th><a href="{modurl modname='Wikula' type='admin' func='pages' sort='user' order=$neworder}" title="Sort by last author">{gt text='Latest author'}</a></th>
      <th><a href="{modurl modname='Wikula' type='admin' func='pages' sort='time' order=$neworder}" title="Sort by edit time">{gt text='Last edit'}</a></th>
      <th>{gt text='Note'}</th>
      {*<th class="number  c1" __title="Hits">{img src='stock_about.png' __alt='Hits'}</th>*}
      <th class="number  c2" title="Sort by number of revisions"><a href="{modurl modname='Wikula' type='admin' func='pages' sort='revisions' order=$neworder}" title="Sort by number of revisions">{img src='stock_book_open.png' __alt='Revisions'}</a></th>
      <th class="number  c3" title="Comments"><a href="{modurl modname='Wikula' type='admin' func='pages' sort='comments' order=$neworder}" title="Sort by number of comments">{img src='stock_help-agent.png' __alt='Comments'}</a></th>
      <th class="number  c4" title="Backlinks"><a href="{modurl modname='Wikula' type='admin' func='pages' sort='backlinks' order=$neworder}" title="Sort by number of backlinks">{img src='stock_link.png' __alt='Backlinks'}</a></th>
      <th class="number  c5" title="Referrers"><a href="{modurl modname='Wikula' type='admin' func='pages' sort='referrers' order=$neworder}" title="Sort by number of Referers">{img src='stock_internet.png' __alt='Referrers'}</a></th>
      <th class="center">{gt text='Actions'}</th>

    </tr>
  </thead>
  <tbody>
  {foreach item='item' from=$items}
    <tr class="{cycle values=',alt'}">
      <td><input type="checkbox" name="id_10356" title="Select {$item.tag}" /></td>
      <td><a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="{$item.tag}">{$item.tag}</a></td>
      <td>{if $item.owner neq '(Public)'}<a href="{modurl modname='Wikula' tag='MyPages' uname=$item.owner|urlencode}" title="{$item.owner|safehtml}">{/if}{$item.owner}{if $item.owner neq '(Public)'}</a>{/if}</td>
      <td><a href="user.php?op=userinfo&amp;uname={$item.user|safehtml|urlencode}" title="{$item.user|safehtml}">{$item.user}</a></td>
      <td class="time">{$item.time}</td>
      <td class="time" title="[{$item.note}]">{$item.note|default:"[Empty note]"}</td>
      {*<td class="number  c1">0</td>*}
      {*modapifunc modname='Wikula' type='admin' func='CountRevisions' tag=$item.tag assign='revcount'*}
      <td class="number  c2"><a href="{modurl modname='Wikula' func='history' tag=$item.tag|urlencode}" title="Display History for UserAdmin ({$item.revisions})">{$item.revisions}</a></td>
      {*modapifunc modname='EZComments' type='user' func='countitems' mod='wikula' objectid=$item.tag assign='comcount'*}
      <td class="number  c3"><a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="Display comments for {$item.tag} ({$item.comments})">{$item.comments}</a></td>
      {*modapifunc modname='Wikula' type='user' func='CountBackLinks' tag=$item.tag assign='blcount'*}
      <td class="number  c4"><a href="{modurl modname='Wikula' func='backlinks' tag=$item.tag|urlencode}" title="Display pages linking to {$item.tag} ({$item.backlinks})">{$item.backlinks}</a></td>
      {*modapifunc modname='Wikula' type='user' func='CountReferers' tag=$item.tag assign='refcount'*}
      <td class="number  c5"><a href="{modurl modname='Wikula' func='Referrers' tag=$item.tag|urlencode}" title="Display external sites linking to {$item.tag} ({$item.referrers})">{$item.referrers}</a></td>
      <td class="center "><a href="{modurl modname='Wikula' func='edit' tag=$item.tag|urlencode}" title="Edit {$item.tag}">{gt text="Edit"}</a> ::
                          <a href="{modurl modname='Wikula' type='admin' func='delete' tag=$item.tag|urlencode}" title="Delete {$item.tag}">{gt text="Delete"}</a> ::
                          <a href="{modurl modname='Wikula' func='info' tag=$item.tag|urlencode}" title="Display information and statistics for {$item.tag}">{gt text="Info"}</a></td>
    </tr>

  {/foreach}
  </tbody>
  </table>
  {pager show='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='startnum' shift=1}
</div>

</div>
