{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    <div class="page">
        <p class="z-informationmsg">{gt text='Please fill in a valid target page name and an (optional) edit note.'}</p>
    </div>
</div>

{form cssClass="z-form"}
{formvalidationsummary}


    <fieldset>
        <legend>{gt text='Clone'}: <a href="{modurl modname='Wikula' type='User' func='show' tag=$tag|urlencode}">{$tag|hyphen2space|safehtml}</a></legend>
        <div class="z-formrow">
            {formlabel for="to" __text='Clone as'}
            {formtextinput id="to" size="37" maxLength="75" mandatory='true'}
        </div>
        <div class="z-formrow">
            {formlabel for="note" __text='Note'}
            {formtextinput id="note" size="40" maxLength="40" mandatory="$mandatorycomment"}
        </div>
        <div class="z-formrow">
            {formlabel for="edit" __text='Edit after creation'}
            {formcheckbox id="edit"}
        </div>
    </fieldset>
    <div class="z-formbuttons z-buttons">
        {formbutton class="z-bt-ok" commandName="save" __text="Save"}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>



 {/form}

