{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="delete" size="small"}
    <h3>{gt text="Pages Administration"}</h3>
</div>

<div id="wikkaadmin">
    {if $submit}
    <form class="z-form" action="{modurl modname='Wikula' type='admin' func='confirmdeletepage'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="tag"    value="{$tag|urlencode}" />
            <input type="hidden" name="authid" value="{secgenauthkey module='Wikula'}" />
            <fieldset>
                <legend>{gt text='Confirmation prompt'}</legend>
                <p class="z-warningmsg">{gt text='Please confirm the suppression of these revisions. The most recent revision left will be set as "Latest". If there is no revisions left, the page will be completly deleted.'}</p>
                <table class="z-datatable" summary="Choose revisions to delete">
                    <thead>
                        <tr>
                            <th>{gt text="Page name"}</th>
                            <th>{gt text="Owner"}</th>
                            <th>{gt text="Latest author"}</th>
                            <th>{gt text="Last edit"}</th>
                            <th>{gt text="Note"}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach item='revision' from=$revisions}

                        <tr class="{cycle values="z-odd,z-even"}">
                            <td>
                                <a href="{modurl modname='Wikula' tag=$revision.tag|urlencode}" title="{$revision.tag}">{$revision.tag}</a>
                                <input type="hidden" name="revids[{$revision.id}]" value="on" />
                            </td>
                            <td>{if $item.owner neq '(Public)'}<a href="{modurl modname='Wikula' tag='MyPages' uname=$revision.owner|urlencode}" title="{$revision.owner|safehtml}">{/if}{$revision.owner}{if $revision.owner neq '(Public)'}</a>{/if}</td>
                            <td><a href="user.php?op=userinfo&amp;uname={$revision.user|safehtml|urlencode}" title="{$revision.user|safehtml}">{$revision.user}</a></td>
                            <td class="time">{$revision.time}</td>
                            <td class="time" title="[{$revision.note}]">{$revision.note|default:"[Empty note]"}</td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>
                <div class="z-buttons z-center">
                    <input class="z-bt-delete" type="submit" name="deleterevisions" value="Confirm" />
                    <a class="z-bt-cancel" href="{modurl modname='Wikula' type='admin' func='pages'}" title="{gt text='Cancel'}">{gt text='Cancel'}</a>
                </div>
            </fieldset>
        </div>
    </form>
    {else}
    <form class="z-form" action="{modurl modname='Wikula' type='admin' func='delete'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="authid" value="{secgenauthkey module="Wikula"}" />
            <fieldset>
                <legend>{gt text='Confirmation prompt'}</legend>
                <p class="z-warningmsg">{gt text='Delete selected revisions.'}</p>
                <table class="z-datatable" summary="Choose revisions to delete">
                    <thead>
                        <tr>
                            <th>{gt text="Page name"}</th>
                            <th>{gt text="Owner"}</th>
                            <th>{gt text="Latest author"}</th>
                            <th>{gt text="Last edit"}</th>
                            <th>{gt text="Note"}</th>
                            <th class="z-center">{gt text="Actions"}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach item='revision' from=$revisions}
                        <tr class="{cycle values="z-odd,z-even"}">
                            <td>
                                <a href="{modurl modname='Wikula' tag=$revision.tag|urlencode}" title="{$revision.tag}">{$revision.tag}</a>
                                <input type="hidden" name="tag" value="{$revision.tag}" />
                            </td>
                            <td>{if $item.owner neq '(Public)'}<a href="{modurl modname='Wikula' tag='MyPages' uname=$revision.owner|urlencode}" title="{$revision.owner|safehtml}">{/if}{$revision.owner}{if $revision.owner neq '(Public)'}</a>{/if}</td>
                            <td><a href="user.php?op=userinfo&amp;uname={$revision.user|safehtml|urlencode}" title="{$revision.user|safehtml}">{$revision.user}</a></td>
                            <td class="time">{$revision.time}</td>
                            <td class="time" title="[{$revision.note}]">{$revision.note|default:"[Empty note]"}</td>
                            <td class="z-center"><input type="checkbox" name="revids[{$revision.id}]" title="Select {$revision.tag}" /></td>
                        </tr>
                        {/foreach}
                    </tbody>
                </table>

                <div class="z-buttons z-center">
                    <input class="z-bt-delete" type="submit" name="submit" value="Delete selected revisions" />
                </div>
            </fieldset>
        </div>
    </form>
    {/if}
</div>

{adminfooter}
