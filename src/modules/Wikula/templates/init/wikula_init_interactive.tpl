{admincategorymenu}
<div class="z-adminbox">
    <h1>{$modinfo.displayname}</h1>
    {modulelinks modname='extenions' type='admin'}
</div>

<div class="z-admincontainer">

<div class="z-adminpageicon">{icon type="config" size="large"}</div>


{insert name='getstatusmsg'}

<h2>{gt text='Wikula Installation'}</h2>
<div>

<div class="z-informationmsg">
    {gt text='If you have any trouble with the installation of Wikula,<br />please refer to <a href="https://github.com/phaidon/Wikula" title="Wikula at Cozi">Wikula@Cozi</a> for support and documentation.'}
</div>



<form class="z-form" action="{modurl modname='wikula' type='init' func='interactiveinit'}" method="post" enctype="application/x-www-form-urlencoded">
<div>
  <input type="hidden" name="authid" value="{secgenauthkey module='wikula'}" />
  <input type="hidden" name="wikulainit[wikkawiki]" value="{if $wikkawiki}1{else}0{/if}" />
  <input type="hidden" name="csrftoken" value="{$csrftoken}" />

  {if $ezcavailable eq false}
  <input type="hidden" name="wikulainit[ezc]" value="0" />
  {/if}
  {if $wikkawiki eq true}
  <input type="hidden" name="wikulainit[wikkaprefix]" value="{$wikkaprefix|safehtml}" />
  {/if}
<fieldset>
                <legend>{gt text='Wikula needs some information in order to proceed with the installation'}</legend>

  <div class="z-formrow">
    <label for="wikula_root_page">{gt text='Root Page'}</label>&nbsp;
    <input id="wikula_bold" type="text" name="wikulainit[root_page]" size="30" value="{$root_page|safehtml}" />
    <p class="z-formnote z-informationmsg">{gt text='Choose a WikiWord (also known as CamelCase) that will be the tag of your Wikula home page. (default: HomePage)'}</p>
  </div>


  <div class="z-formrow">
    <label for="wikula_itemsperpage">{gt text='Items per page'}</label>&nbsp;
    <input id="wikula_itemsperpage" type="text" name="wikulainit[itemsperpage]" size="3" value="{$itemsperpage|safehtml}" />
  </div>


  <div class="z-formrow">
    <label for="wikula_savewarning">{gt text='Receive notifications when a new Page/Revision is saved?'}</label>&nbsp;
    <input id="wikula_savewarning" name="wikulainit[savewarning]" type="checkbox" value="1"{if $savewarning} checked="checked"{/if} />
  </div>


  <div class="z-formrow">
    <label for="wikula_logreferers">{gt text='Log Referers - Note: If the Zikula HTTPReferers module is available, it will use the exclusions setting.)'}</label>&nbsp;
    <input id="wikula_logreferers" name="wikulainit[logreferers]" type="checkbox" value="1"{if $logreferers} checked="checked"{/if} />
  </div>


  {if $ezcavailable eq true}
  <div class="z-formrow">
    <label for="wikula_ezc">{gt text='Wikula detected the EZComments module, do you want to activate the EZComments Hook for Wikula?'}</label>&nbsp;
    <input id="wikula_ezc" name="wikulainit[ezc]" type="checkbox" value="1"{if $ezc} checked="checked"{/if} />
  </div>

  {/if}

  {if $wikkawiki eq false}
  <div class="z-formrow">
    <label for="wikula_wikkaprefix">{gt text='Wikula did not detect a stand alone Wikkawiki installation. If you have one, please provide the table prefix (default: wikka)'}</label>&nbsp;
    <input id="wikula_wikkaprefix" name="wikulainit[wikkaprefix]" type="text" value="wikka" size="20" value="{$wikkaprefix}" />
  </div>

  {/if}

  <div class="z-formrow">
    {if $wikkawiki eq false}
    <label for="wikula_importwikka">{gt text='Select yes if you want Wikula to attempt to import the pages content'}</label>
    {else}
    <label for="wikula_importwikka">{gt text='Wikula detected a Stand alone WikkaWiki installation, would you like to import the pages into Wikula?'}</label>
    {/if}
    <select id="wikula_importwikka" name="wikulainit[importwikka]">
      <option value="0"{if $importwikka eq 0} selected="selected"{/if}>{gt text='No'}</option>
      <option value="1"{if $importwikka eq 1} selected="selected"{/if}>{gt text='Yes'}</option>
    </select>
  </div>


  <div class="z-formrow">
    <label for="wikula_admincategory">{gt text='In which category would you like to place Wikula?'}</label>
    <select id="wikula_admincategory" name="wikulainit[admincategory]">
      {html_options options=$admincats selected=$admincat}
    </select>
  </div>


  <div class="z-formrow">
    <label for="wikula_activate">{gt text='Activate module after installation?'}</label>
    <input id="wikula_activate" name="wikulainit[activate]" type="checkbox" value="1"{if $activate} checked="checked"{/if} />
  </div>


  <div style="float:right; width: 48%; margin: 0.5%; padding: 0.5%;">
  {img src='wizard.gif' __alt='wizard' modname='wikula'}
  </div>

  <div class="z-formbuttons">
    <input name="submit" type="submit" value="{gt text='Submit'}" />
  </div>
  </fieldset>
</div>
</form>
</div>
