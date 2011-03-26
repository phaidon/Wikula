{* $Id: wikula_init_delete.tpl 83 2008-12-17 04:04:58Z mateo $ *}
{admincategorymenu}

<div class="pn-admincontainer" style="text-align:center;">
<div class="pn-adminpageicon">{img modname='core' src='package.gif' set='icons/large' __alt='Deinstallation of Wikula' }</div>
<h2>{gt text='Deinstallation of Wikula'}</h2>

{gt text='Thank you for trying Wikula.<br />All the module tables will be removed now!'}
<br />
{img src='wizard_reverse.gif' __alt='wizard' modname='wikula'}
<br /><br />

{pnsecgenauthkey module='Modules' assign='authid'}
<a href="{modurl modname=Modules type=admin func=remove authid=$authid}" title="{gt text='Continue'}">{gt text='Continue'}</a> {gt text='or'}
<a href="{modurl modname=Modules type=admin func=view}" title="{gt text='Cancel'}">{gt text='Cancel'}</a>

</div>
