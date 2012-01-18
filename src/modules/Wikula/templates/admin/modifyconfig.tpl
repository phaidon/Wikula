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
    
    <p class="z-formnote">
        <a href="{modurl modname='Wikula' type='admin' func="rebuildLinksAndCategoriesTables"}">Rebuild links and categories tables.</a>
    </p>
    
</fieldset>
    
<fieldset>
    <legend>{gt text='Page edit'}</legend>
    <div class="z-formrow">
        {formlabel for="mandatorycomment" __text='Page edit comments are mandatory'}
        {formcheckbox id="mandatorycomment"}
    </div>
    
    <div class="z-formrow">
        {formlabel for="showeditnote" __text='Show a (license) note'}
        {formcheckbox id="showeditnote" onChange="Zikula.checkboxswitchdisplaystate('showeditnote', 'editnote_container', true)"}
    </div>
        
    <div id="editnote_container">
        <div class="z-formrow" id="textarea_container">
            {formlabel for="editnote" text=' '}
            {formtextinput  textMode="multiline" id="editnote"}
            <div style="z-index:999">
                {notifydisplayhooks eventname='wikula.ui_hooks.editor.display_view' id='editnote'}
            </div>
        </div>
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