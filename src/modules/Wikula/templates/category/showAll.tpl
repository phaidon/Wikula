<div id="wikulaheader">
    <div class="header">
        <h2>
            <a href="{modurl modname=$modinfo.name type='user' func='main'}">
                {$modinfo.displayname}
            </a> &#187;
            {gt text="Categories"}
        </h2>
        {include file='user/menulight.tpl'}
    </div>
</div>


<ul style="margin-left:20px">
    {foreach from=$categories item="category"}
        <li><a href="{modurl modname='Wikula' type='category' func='show' category=$category.category}">{$category.category}</a></li>
    {/foreach}
</ul>
