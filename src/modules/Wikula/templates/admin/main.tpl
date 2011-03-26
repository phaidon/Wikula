{include file='admin/header.tpl'}
{gt text='Wikula Statistics' assign=templatetitle}
<div class="z-adminpageicon">{icon type="view" size="large"}</div>
<h2>{$templatetitle}</h2>


<dl>
	<dt>{gt text='Pages'}:</dt>
	<dd>{$pagecount}</dd>
<dt>{gt text='Owners'}:</dt>
	<dd>{gt text='Total'} ({$ownerscount})
		<ul>
		{foreach item='owner' from=$owners}
			<li>{$owner}</li>
		{/foreach}
		</ul>
	</dd>
</dl>

</div>

{include file='admin/footer.tpl'}
