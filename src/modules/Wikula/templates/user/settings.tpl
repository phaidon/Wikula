<h2>{gt text='Wiki Setting'}</h2>

{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>
    <legend>{gt text='Settings'}</legend>
    {if $modvars.Wikula.subscription}
    <div class="z-formrow">
        {formlabel for="subscribe" __text='Subscribe wiki'}
        {formcheckbox id="subscribe"}
    </div>
    {/if}
</fieldset>

<div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/form}