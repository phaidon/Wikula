{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    <div class="page">

        <p class="z-clearfix">
            <a class="z-icon-es-rss z-floatright" href="{modurl modname='Wikula' type='user' func='RevisionsXML' tag=$tag theme='rss'}">
                {gt text='Show feed of these changes'}
            </a>
        </p>

        <p class='z-informationmsg'>
            {gt text='The artice %s has so far %s revisions.' tag1=$tag tag2=$revisions|@count}
        </p>
                
        <form action="{modurl modname='Wikula' type='user' func='history' tag=$tag}" class="z-form" method="post" enctype="application/x-www-form-urlencoded">
            <table class="z-datatable">
                <thead>
                    <tr>
                        <th colspan=2 width=0%>
                            <input type='submit' value='{gt text="Compare"}' />
                        </th>
                        <th width=10%>
                            {gt text='Creator'}
                        </th>
                        <th width=90%>

                        </th>
                        <th width=0%>
                            {gt text="Actions"}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {foreach item='revision' from=$revisions}
                        <tr class="{cycle values="z-odd,z-even"}">
                            <td>
                                <input type='radio' name='a' value={$revision.id} 
                                       {if $a == $revision.id}checked='checked'{/if} />
                            </td>
                            <td>
                                <input type='radio' name='b' value={$revision.id} 
                                       {if $b == $revision.id}checked='checked'{/if} />
                            </td>
                            <td nowrap>
                                {img src='user.png' modname='core' set='icons/extrasmall' } {$revision.user|profilelinkbyuname}
                            </td>
                            <td>
                                <em>{$revision.time|dateformat:datetimelong}</em>: <strong>{$revision.note}</strong>
                            </td>
                            <td>
                                <a href="{modurl modname='Wikula' type='user' func='show' rev=$revision.id|urlencode}" __title="Display article">
                                    {img src='demo.png' modname='core' set='icons/extrasmall' }
                                </a>
                            </td>
                        </tr>
                    {/foreach}

                </tbody>
            </table>
        </form>
                    
        <br />
        {if is_array($diff)}
            <table class="z-datatable">
                <thead>
                    <tr>
                        <th colspan="3">
                            {gt text='Revision comparison'}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    {foreach item='diffline' from=$diff}
                        {assign var='diffaction' value='nochange'}
                        {if substr($diffline, 0, 1)  == '+'}
                            {assign var='diffaction' value='insert'}
                        {elseif substr($diffline, 0, 1) == '-'}
                            {assign var='diffaction' value='delete'}
                        {/if}  
                        <tr class="wikula-diff-{$diffaction}">
                            <td width=10px>
                                {if substr($diffline, 0, 1) != '+'}
                                    {counter name='i'}
                                {/if}
                            </td>
                            <td width=10px>
                                {if substr($diffline, 0, 1) != '-'}
                                    {counter name='j'}
                                {/if}
                            </td> 
                            <td>
                                {$diffline} 
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
           </table>
        {/if}
</div>