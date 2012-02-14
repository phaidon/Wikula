{adminheader}

<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text="Select an engine"}</h3>
</div>

{form cssClass="z-form"}
{formvalidationsummary}


{if $engine == 'none'}
    <p class="z-errormsg">
        {gt text="There is no engine set! Without engine Wikula is very limited!"}
    </p>    
{/if}

<div class="z-informationmsg">
    <p>
        {gt text='The engine transforms the content of a wiki page from a lightweight markup language into html code. At the moment there are two engines available:'}
    </p>
    <p>
        * {gt text='Wikka is the orignal engine from the WikkaWiki. It assures a full backward compatibilty to Wikula 1.x.'}
        <a href='https://github.com/phaidon/Wikula/tree/master/src/modules/Wikka'>{gt text='Website'}</a>
    </p>
    <p>
        * {gt text='LuMicuLa is a newer engine. It supports several lightweight markup languages like Wikka, Creole and BBCode.'}
        <a href='https://github.com/phaidon/LuMicuLa'>{gt text='Website'}</a>
    </p>
</div>

<fieldset>
    <div class="z-formrow">
        {formlabel for="engine" __text="Engine"}
        {formdropdownlist id="engine" items=$engines}
    </div>

    {if $engine != 'none'}
        <p class="z-formnote">
            <a href="{modurl modname=$engine type="admin" func="main"}">
                {gt text="Configure current engine"} ({$engine})
            </a>
        </p>
    {/if}
    
    <p class="z-formnote">
        <a href="{modurl modname='Wikula' type='admin' func="rebuildLinksAndCategoriesTables"}">
            {gt text='Rebuild links and categories tables'}
        </a>
    </p>

</fieldset>          
            
<div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>

{/form}

{adminfooter}