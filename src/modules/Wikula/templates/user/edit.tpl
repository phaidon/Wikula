{include file='user/menu.tpl' tag=$tag}

{form cssClass="z-form z-linear"}
{formvalidationsummary}

<div id="wikula">

    {if isset($preview)}
    <div id="wikula_editpreview">
        [ <a href="#wikula_editform">{gt text='Go to the edit form'}</a> ]
        <br /><br />
        {$preview|notifyfilters:'wikula.filter_hooks.body.filter'}
    </div>
    {/if}

    <!-- We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
    // hence htmlspecialchars() instead of htmlspecialchars_ent() which UNescapes entities! -->
    <div class="z-formrow" id="textarea_container">
        {formtextinput id="body" textMode="multiline" rows=4 cols=100 style="width:98%;height:500px;"}
        {formtextinput id="id" textMode="hidden" size="11" maxLength="11"}
        <div style="z-index:999">
        {notifydisplayhooks eventname='wikula.ui_hooks.editor.display_view' id='body'}
        </div>
    </div>
    
    <!-- note add Edit
    // We need to escape ALL entity refs before display so we display them _as_ entities instead of interpreting them
    // so we use htmlspecialchars on the edit note (as on the body)-->
    {gt text='Please add a note with details of your submission'}:<br />
    <div class="z-formrow">
        {formtextinput id="note" size="40" maxLength="40" style="width: 98%;" mandatory="$mandatorycomment"}
    </div>

    <br />
    <!--//finish-->

    <div class="z-formbuttons z-buttons">
        {formbutton class="z-bt-ok"                    commandName="save"    __text="Save"}
        {formbutton class="z-bt-icon wikula-bt-clone"  commandName="clone"   __text="Clone"}
        {formbutton class="z-bt-edit"                  commandName="rename"  __text="Rename"}
        {formbutton class="z-bt-preview"               commandName="preview" __text="Preview"}
        {formbutton class="z-bt-cancel"                commandName="cancel"  __text="Cancel"}
    </div>

</div>

{/form}