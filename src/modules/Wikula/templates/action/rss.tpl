{assign var='link' value=$feed->get_image_link()}
{if $feedimage AND $link neq ''}
<div class="rss-image">
	{assign var='image' value=$feed->get_image_url()}
	{assign var='title' value=$feed->get_image_title()}
	{if $link neq ''}
	<a href="{$link}"><img src="{$image}" alt="{$title}" /></a>
	{else}
	<img src="{$image}" alt="{$title}" />
	{/if}
</div>
{/if}

{if !empty($feedname)}
<p><strong>{gt text='Name'}</strong> : {$feedname|safehtml}</p>
{/if}
<p><strong>{gt text='URL'}</strong> : {$feedurl|safehtml}</p>
<div>
		{assign var='feeditems' value=$feed->get_items()}
		{foreach from=$feeditems item='feeditem'}
		<div>
			<div>
			{assign var='feeditemlink' value=$feeditem->get_link()}
			{assign var='feeditemdescription' value=$feeditem->get_description()}
			{assign var='feeditemtitle' value=$feeditem->get_title()}
			{assign var='feeditemauthor' value=$feeditem->get_author()}
			<a href="{$feeditemlink}" {if $feednewwin eq 1}target="_blank"{/if}>{$feeditemtitle|utf8decode}</a>
			{if $feeditemauthor neq ''}
			{gt text='Feeds by %s' tag1=$feeditemauthor}
			{/if}
			</div>
			{if $feeditemdescription neq ''}
				<p>
				{$feeditemdescription|safehtml}
				</p>
				<div style="text-align:right"><a href="{$feeditemlink}" {if $feednewwin eq 1}target="_blank"{/if}>{gt text='Read more'}</a></div>
			{/if}
		</div>
	{/foreach}
</div>
