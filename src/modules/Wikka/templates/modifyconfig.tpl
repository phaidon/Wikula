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
        {formlabel for="editor" __text="Editor"}
        {formdropdownlist id="editor" items=$editors}
    </div>

    <div class="z-formrow">
        {formlabel for="showIndex" __text='Show a index on wiki pages'}
        {formcheckbox id="showIndex"}
    </div>

</fieldset>

    
<fieldset>
    <div class="z-formrow">
        {formlabel for="syntaxHighlighter" __text="Syntax highlighting" id="syntaxHighlighterLabel"}
        <script type="text/javascript">
            var defaultTooltip2 = new Zikula.UI.Tooltip(
                                    $('syntaxHighlighterLabel'),
                                    '{{img src='code_highlighting.jpg' alt='' modname=$modinfo.name}}'
                                  );
        </script>            
        {formdropdownlist id="syntaxHighlighter" items=$syntaxHighlighters}
    </div>

</fieldset>    
    
<div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/form}
{adminfooter}