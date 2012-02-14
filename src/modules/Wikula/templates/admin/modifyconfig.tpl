{adminheader}

<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text="Modify configuration"}</h3>
</div>

{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>
    <legend>{gt text='General'}</legend>
    <div class="z-formrow">
        {formlabel for="root_page" __text='Root Page'}
        {formtextinput id="root_page" size="20" maxLength="64"}
    </div>

    <div class="z-formrow">
        {formlabel for="subscription" __text='Enable subscribtions'}
        {formcheckbox id="subscription"}
    </div>

    <div class="z-formrow">
        {formlabel for="itemsperpage" __text='Items per page'}
        {formintinput id="itemsperpage" size="3"}
    </div>

    <div class="z-formrow">
        {formlabel for="single_page_permissions" __text='Enable single page permissions'}
        {formcheckbox id="single_page_permissions"}
    </div>

</fieldset>

<fieldset>
    <legend>{gt text='Search'}</legend>
    
    <div class="z-formrow">
        {formlabel for="ajaxsearch" __text='Enable live search'}
        {formcheckbox id="ajaxsearch"}
        <em class="z-formnote z-sub">{gt text="Improve search experience, but needs more performance."}</em>
    </div>
    
    <div class="z-formrow">
        {formlabel for="fulltextsearch" __text='Enable full text search'}
        {formcheckbox id="fulltextsearch"}
        <em class="z-formnote z-sub">{gt text="Improve search experience, but needs more performance."}</em>
    </div>
    
</fieldset>

        
        
{if $editor == 'none'}
    <p class="z-errormsg">
        {gt text="There is no editor set! Without editor Wikula is very limited!"}
    </p>    
{/if}
    
<fieldset>
    <legend>{gt text='Editor'}</legend>
    <div class="z-formrow">
        {formlabel for="editor" __text="Editor"}
        {formdropdownlist id="editor" items=$editors}
        {if $editor != 'none'}
            <em>
                <a href="{modurl modname=$editor type="admin" func="main"}">
                   {gt text="Editor settings"}
                </a>
            </em>
        {/if}
    </div>
    
    <div class="z-formrow">
        {formlabel for="mandatorycomment" __text='Page edit comments are mandatory'}
        {formcheckbox id="mandatorycomment"}
    </div>

    <div class="z-formrow">
        {formlabel for="showeditnote" __text='Show a (license) note'}
        {formcheckbox id="showeditnote" onchange="Zikula.checkboxswitchdisplaystate('showeditnote', 'editnote_container', true)"}
    </div>

    <div id="editnote_container">
        <div class="z-linear" id="textarea_container">
            {formtextinput  textMode="multiline" id="editnote" rows='5' cols='60'}
            <div style="z-index:999">
                {notifydisplayhooks eventname='wikula.ui_hooks.editor.display_view' id='editnote'}
            </div>
        </div>
    </div>
</fieldset>

{if $engine == 'none'}
    <p class="z-errormsg">
        {gt text="There is no engine set! Without engine Wikula is very limited!"}
    </p>    
{/if}
            
<fieldset>
    <legend>{gt text='Engine'}</legend>
    <div class="z-formrow">
        {formlabel for="engine" __text="Engine"}
        {formdropdownlist id="engine" items=$engines}
        {if $engine != 'none'}
            <em>
                <a href="{modurl modname=$engine type="admin" func="main"}">
                   {gt text="Engine settings"}
                </a>
            </em>
        {/if}
    </div>

    <p class="z-formnote">
        <a href="{modurl modname='Wikula' type='admin' func="rebuildLinksAndCategoriesTables"}">Rebuild links and categories tables.</a>
    </p>

</fieldset>
    
    
<fieldset>
    <legend>{gt text='Discussion'}</legend>
    <div class="z-formrow">
        {formlabel for="discussion" __text="Discussion module"}
        {formdropdownlist id="discussionModule" items=$discussionModules}
        {if $discussionModule != 'none'}
            <em>
                <a href="{modurl modname=$discussionModule type="admin" func="main"}">
                   {gt text="Discussion module settings"}
                </a>
            </em>
        {/if}
    </div>
</fieldset> 
            
            
            
<div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/form}

<script type="text/javascript">

    if (!$('showeditnote').checked) {
        $('editnote_container').hide();
    }

</script>

{adminfooter}