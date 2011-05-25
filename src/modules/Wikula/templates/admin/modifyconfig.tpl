{gt text='Modify configuration' assign=templatetitle}
{gt text='config' assign=templateicon}
{include file='admin/header.tpl'}

{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>
       
    <div class="z-formrow">
        {formlabel for="root_page" __text='Root Page'}
        {formtextinput id="root_page" size="20" maxLength="64"}
    </div>

    <div class="z-formrow">
        {formlabel for="subscription" __text='Enable subscribtions'}
        {formcheckbox id="subscription"}
    </div>

    <div class="z-formrow">
        {formlabel for="hidehistory" __text='Do not include page history info box into wiki pages'}
        {formcheckbox id="hidehistory"}
    </div>

    <div class="z-formrow">
        {formlabel for="excludefromhistory" __text='Page tags, separated with comma, that should always be displayed without page history info box'}
        {formtextinput id="excludefromhistory" size="64" maxLength="64"}
    </div>

    <div class="z-formrow">
        {formlabel for="hideeditbar" __text='Do not show the editor help bar (wiki-edit) above an articled that gets edited'}
        {formcheckbox id="hideeditbar"}
    </div>


    <div class="z-formrow">
        {formlabel for="logreferers" __text='Log Referers - Note: If the Zikula HTTPReferers module is available, it will use the exclusions setting.)'}
        {formcheckbox id="logreferers"}
    </div>
    <div style="clear:both"></div>
    <div class="z-formrow">
        {formlabel for="itemsperpage" __text='Items per page'}
        {formintinput id="itemsperpage" size="3"}
    </div>


    <div class="z-formbuttons z-buttons">
          {formbutton class="z-bt-ok" commandName="save" __text="Save"}
          {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>

</fieldset>
{/form}

{include file='admin/footer.tpl'}