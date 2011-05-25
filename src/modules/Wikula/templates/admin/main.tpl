{gt text='Statistics' assign=templatetitle}
{gt text='home' assign=templateicon}
{include file='admin/header.tpl'}

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
