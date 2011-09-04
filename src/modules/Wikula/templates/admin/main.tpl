{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="info" size="small"}
    <h3>{gt text="Statistics"}</h3>
</div>


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

{adminfooter}
