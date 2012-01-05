{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    <div class="page">
        <p class="z-informationmsg">{gt text='Please fill in a valid target page name.'}</p>
    </div>
</div>

{form cssClass="z-form"}
{formvalidationsummary}


    <fieldset>
        <legend>{gt text='Rename'}: <a href="{modurl modname='Wikula' type='User' func='show' tag=$tag|urlencode}">{$tag|hyphen2space|safehtml}</a></legend>
        <div class="z-formrow">
            {formlabel for="to" __text='Rename as'}
            {formtextinput id="to" size="37" maxLength="75" mandatory='true'}
        </div>
    </fieldset>
    <div class="z-formbuttons z-buttons">
        {formbutton class="z-bt-ok" commandName="save" __text="Save"}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>


 {/form}

