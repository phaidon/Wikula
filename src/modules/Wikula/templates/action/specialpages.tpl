<ul>
    {foreach from=$specialpages key='tag' item='specialpage'}
    <li>
        <a href="{modurl modname=$modinfo.name type='user' func='main' tag=$tag|urlencode}">{$tag|hyphen2space}</a>{if array_key_exists('description', $specialpage)}: {$specialpage.description}{/if}
    </li>
    {/foreach}
</ul>