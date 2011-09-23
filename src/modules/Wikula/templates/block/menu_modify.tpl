{ajaxheader modname='Wikula' filename='chosen/chosen.proto.min.js'}
{pageaddvar name='stylesheet' value='modules/Wikula/javascript/chosen/chosen.css'}

<div id="chosenCss" class="z-formrow">
    <label for="menupages">{gt text='Menu pages'}</label>
    <select multiple="multiple" id="menupages" class="chzn-select" name="menupages[]">
        {foreach from=$pages item="page"}
        <option value="{$page.tag}" {if in_array($page.tag, $menupages)}selected="selected"{/if} >{$page.tag}</option>
        {/foreach}
    </select>
</div>

<script type="text/javascript">
    New Chosen($("chzn_select_field"),{no_results_text: "No results matched"});
</script>