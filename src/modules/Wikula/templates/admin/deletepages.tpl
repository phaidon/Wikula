{adminheader}

<div class="z-admin-content-pagetitle">
    {icon type="delete" size="small"}
    <h3>{gt text="Pages Administration"}</h3>
</div>

<div id="wikkaadmin">
    {form cssClass="z-form"}
    {formvalidationsummary}

    
        <fieldset class="z-linear">
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
                            <a href="{modurl modname='Wikula' type='user' func='show' tag=$revision.tag|urlencode}" title="{$revision.tag}">{$revision.tag}</a>
                            <input type="hidden" name="tag" value="{$revision.tag}" />
                        </td>
                        <td>{if $revision.owner neq '(Public)'}<a href="{modurl modname='Wikula' type='user' func='show' tag='MyPages' uname=$revision.owner|urlencode}" title="{$revision.owner|safehtml}">{/if}{$revision.owner}{if $revision.owner neq '(Public)'}</a>{/if}</td>
                        <td><a href="user.php?op=userinfo&amp;uname={$revision.user|safehtml|urlencode}" title="{$revision.user|safehtml}">{$revision.user}</a></td>
                        <td class="time">{$revision.time|dateformat:datetimebrief}</td>
                        <td class="time" title="[{$revision.note}]">{$revision.note|default:"[Empty note]"}</td>
                        <td class="z-center">{formcheckbox id=$revision.id group="revisionids"}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
                    
            <a href="javascript:void(0);" id="wikula_select_all">{gt text="Check all"}</a> / 
            <a href="javascript:void(0);" id="wikula_deselect_all">{gt text="Uncheck all"}</a>
            <br /><br />

                
            <div class="z-formbuttons z-buttons">
                {formbutton class="z-bt-ok" commandName="delete" __text="Delete selected revisions"}
                {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
            </div>
            <br />
        </fieldset>
    {/form}
</div>

{adminfooter}

<script type="text/javascript">
    $('wikula_select_all').observe('click', function(e){
        Zikula.toggleInput('.z-form-checkbox', true);
        e.stop();
    });
    $('wikula_deselect_all').observe('click', function(e){
        Zikula.toggleInput('.z-form-checkbox', false);
        e.stop();
    });
</script>
