{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text="Modify configuration"}</h3>
</div>

{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>
    <legend>{gt text='Settings'}</legend>
    <div class="z-formrow">
        {formlabel for="root_page" __text='Root Page'}
        {formtextinput id="root_page" size="20" maxLength="64"}
    </div>

    <div class="z-formrow">
        {formlabel for="subscription" __text='Enable subscribtions'}
        {formcheckbox id="subscription"}
    </div>


    <div class="z-formrow">
        {formlabel for="mandatorycomment" __text='Page edit comments are mandatory'}
        {formcheckbox id="mandatorycomment"}
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

<div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/form}

{adminfooter}