<div id="wikula">

{foreach from=$pages item="page"}
<a href="{modurl modname="wikula" tag=$page}">{$page}</a><br />
{/foreach}<br />

<form action="{textsearchlink}" method="post" enctype="application/x-www-form-urlencoded">

<div style="">
    <input id="wikula_phrase" name="phrase" size="12" class="searchbox" style="width:170px"/>
</div>
</form>

<br />

</div>