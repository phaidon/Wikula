<ul>
    {foreach from=$pages item='page'}
        <li><a href="{modurl modname="Wikula" type="user" func="show" tag=$page.tag}">{$page.tag|safetext}</a></li>
    {/foreach}
    <li style="border-top-style:solid;border-width:1px" id="containing">
            <a href="{modurl modname="Wikula" type="user" func="show" tag="Search" phrase=$phrase}">
                {gt text='containing...'}<br />
                <i>{$phrase|safetext}</i>
            </a>
    </li>
</ul>