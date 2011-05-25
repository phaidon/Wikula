{gt text='Wiki Setting' assign=templatetitle}
<h2>{$templatetitle}</h2>
<br />

{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>

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

{include file='admin/footer.tpl'}