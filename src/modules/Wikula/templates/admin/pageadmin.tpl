{ajaxheader modname='Wikula' filename='tablekit/js/fabtabulous.js'}
{ajaxheader modname='Wikula' filename='tablekit/js/tablekit.js'}
{pageaddvar name='stylesheet' value='modules/Wikula/javascript/tablekit/css/style.css'}




{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="view" size="small"}
    <h3>{gt text="Page Administration"}</h3>
</div>


<div id="wikkaadmin">
    <form action="{modurl modname='Wikula' type='admin' func='pages'}" class="z-form" method="post" enctype="application/x-www-form-urlencoded">
        <fieldset>
            <legend>{gt text='Filter view'}</legend>
            <div class="z-clearfix">
                <div class="z-floatright">
                    <label for="wikula_itemsperpage">{gt text='Show'}</label>
                    {html_options id="wikula_itemsperpage" name="itemsperpage" values=$pageroptions output=$pageroptions selected=$itemsperpage}
                    <label for="wikula_itemsperpage">{gt text='Items per page'}</label>
                    <span class="z-buttons"><input class="z-bt-small z-bt-ok" type="submit" value="{gt text='Apply'}" /></span>
                </div>
                <div class="z-floatleft">
                    <label for="wikula_q">{gt text='Search page:'}</label>
                    <input type="text" id="wikula_q" name="q" title="{gt text='Enter search string:'}" size="20" maxlength="50" value="" />
                    <span class="z-buttons"><input class="z-bt-small z-bt-filter" type="submit" value="{gt text='Submit'}" /></span>
                </div>
            </div>
        </fieldset>
    </form>

    <p class="wiki_amount">
        {gt text='Records'} ({$total}): {$startnum}-{math equation='x+y' x=$startnum y=$itemcount-1}
        ({gt text='Sorted by:'} <em>{$sort|default:'time'}, {$order|default:'DESC'}</em>)
    </p>







    <table class="z-datatable sortable">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th class="sortfirstdesc">{gt text='Page name'}</th>
                <th>{gt text='Owner'}</th>
                <th>{gt text='Latest author'}</th>
                <th>{gt text='Last edit'}</th>
                <th>{gt text='Note'}</th>
                {*<th class="number c1" __title="Hits">{img src='stock_about.png' __alt='Hits'}</th>*}
                <th class="number c2" title="Sort by number of revisions">
                    {img src='stock_book_open.png' __alt='Revisions'}
                </th>
                <th class="number c3" title="Comments">
                    {img src='stock_help-agent.png' __alt='Comments'}</a>
                </th>
                <th class="number  c4" title="Backlinks">
                    {img src='stock_link.png' __alt='Backlinks'}
                </th>
                <th class="z-nowrap z-right">{gt text='Actions'}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item='item' from=$items}
            <tr class="{cycle values="z-odd,z-even"}">
                <td><input type="checkbox" name="id_10356" title="Select {$item.tag}" /></td>
                <td><a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="{$item.tag}">{$item.tag}</a></td>
                <td>
                    {if $item.owner neq '(Public)'}
                    <a href="{modurl modname='Wikula' tag='MyPages' uname=$item.owner|urlencode}" title="{$item.owner|safehtml}">
                        {/if}
                        {$item.owner}
                        {if $item.owner neq '(Public)'}
                    </a>
                    {/if}
                </td>
                <td>{$item.user|profilelinkbyuname}</td>
                <td class="time">{$item.time|dateformat}</td>
                <td class="time" title="[{$item.note}]">{$item.note|default:"[Empty note]"}</td>
                {*<td class="number  c1">0</td>*}
                {*modapifunc modname='Wikula' type='admin' func='CountRevisions' tag=$item.tag assign='revcount'*}
                <td class="number  c2">
                    <a href="{modurl modname='Wikula' func='history' tag=$item.tag|urlencode}" title="Display History for UserAdmin ({$item.revisions})">{$item.revisions}</a>
                </td>
                {*modapifunc modname='EZComments' type='user' func='countitems' mod='Wikula' objectid=$item.tag assign='comcount'*}
                <td class="number  c3">
                    <a href="{modurl modname='Wikula' tag=$item.tag|urlencode}" title="Display comments for {$item.tag} ({$item.comments})">{$item.comments}</a>
                </td>
                {*modapifunc modname='Wikula' type='user' func='CountBackLinks' tag=$item.tag assign='blcount'*}
                <td class="number  c4">
                    <a href="{modurl modname='Wikula' func='backlinks' tag=$item.tag|urlencode}" title="Display pages linking to {$item.tag} ({$item.backlinks})">{$item.backlinks}</a>
                </td>
                <td class="z-nowrap z-right">
                    <a href="{modurl modname='Wikula' func='edit' tag=$item.tag|urlencode}" title="Edit {$item.tag}">{icon type="xedit" size="extrasmall"}</a>
                    <a href="{modurl modname='Wikula' type='admin' func='delete' tag=$item.tag|urlencode}" title="Delete {$item.tag}">{icon type="delete" size="extrasmall"}</a>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>


    <script type="text/javascript">
            var table = new TableKit(table, {options});
    </script>

    {pager show='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='startnum' shift=1}
</div>
{adminfooter}