{*  $Id: wikula_init_step3.tpl 83 2008-12-17 04:04:58Z mateo $  *}

<h2>{gt text='Wikula Installation'}</h2>
<div style="text-align:center;" class="z-form">
<fieldset>
    <legend>{gt text='Final step of installation'}</legend>

<br />
{gt text='Thank you for using Wikula! the Wiki module for Zikula'}
<br />
{img src='wizard_reverse.gif' __alt='wizard' modname='wikula'}
<br /><br />

<h3>{gt text='Installation Summary'}</h3>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Root Page'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{$root_page|safehtml}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Items per page'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{$itemsperpage|safehtml}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Save Warning'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{if $savewarning}{gt text='Yes'}{else}{gt text='No'}{/if}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Log Referers'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{if $logreferers}{gt text='Yes'}{else}{gt text='No'}{/if}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Activate EZComments hook'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{if $ezc}{gt text='Yes'}{else}{gt text='No'}{/if}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Import pages from a Wikkawiki?'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{if $importwikka}{gt text='Yes'}{else}{gt text='No'}{/if}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Admin Category'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{$admincategory|safehtml}</strong></div>
</div>
<div class="z-formrow">
    <div style="float:left;  width:49%; text-align:right;">{gt text='Activate module'}: </div>
    <div style="float:right; width:49%; text-align:left;"><strong>{if $activate}{gt text='Yes'}{else}{gt text='No'}{/if}</strong></div>
</div>

<div class="z-formrow">
{pnsecgenauthkey module='Modules' assign='authid'}
<a href="{modurl modname='wikula' type='init' func='step2'}" title="{gt text='Back'}">{gt text='Back'}</a> 
{gt text='or'} 
<a href="{modurl modname='Modules' type='admin' func='initialise' authid=$authid activate=$activate}" title="{gt text='Continue'}">{gt text='Continue'}</a>
</div>
</fieldset>
</div>

