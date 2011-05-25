{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
  <div class="page">

  <a href="{modurl modname='Wikula' type='user' func='RevisionsXML' tag=$tag theme='rss'}">
    {gt text='Show feed'}
  </a><br /><br />

  {assign var='lastauthor' value=''}
  {assign var='lastedit' value=''}

  {foreach name='object' item='object' from=$objects}

    {pnusergetidfromname uname=$object.EditedByUser assign='uid'}
    {pnusergetvar uid=$uid name='_YOURAVATAR' assign='avatar'}

    {if $avatar eq '' OR $avatar eq 'blank.gif'}
      {img modname='wikula' src='avatar_male.gif' width='20' class='avatar'}
    {else}
      <img src="images/avatar/{$avatar|safehtml}" class="avatar" width="20" />
    {/if}

    <strong>
    {if $smarty.foreach.object.first}
      {gt text='Last edit'} 
      {assign var='lastauthor' value=$object.EditedByUser}
      {assign var='lastedit' value=$object.pageAtime}
    {else}
      {gt text='Edit'} 
    {/if}
    <a href="{modurl modname='wikula' func='main' tag=$tag time=$object.pageAtimeurl}" title="{$tag} - {$object.pageAtime}">{$object.pageAtime}</a> 
    {gt text='by'} {$object.EditedByUser|profilelinkbyuname}
    </strong> 
    <span class="pagenote changenote">{if $object.note ne ''}[ {$object.note} ]{/if}</span>
    <br />
    <br />

    {if $object.added neq ''}
      <strong>{gt text='Additions'}</strong><br />
      <span class="additions">{$object.newcontent|transform}</span>
      <br /><br />
    {/if}
    {if $object.deleted neq ''}
      <strong>{gt text='Deletions'}</strong><br />
      <span class="deletions">{$object.oldcontent|transform}</span>
      <br /><br />
    {/if}
    <hr />
  {/foreach}

  {pnusergetidfromname uname=$oldest.user assign='uid'}
  {pnusergetvar uid=$uid name='_YOURAVATAR' assign='avatar'}

  {if $avatar eq '' OR $avatar eq 'blank.gif'}
    {img modname='wikula' src='avatar_male.gif' width='20' class='avatar'}
  {else}
    <img src="images/avatar/{$avatar|safehtml}" class="avatar" width="20" />
  {/if}

  <strong>{gt text='Oldest known version of this page was edited on'} <a href="{modurl modname='wikula' func='main' tag=$tag time=$oldest.time|@urlencode}" title="{$tag} - {$oldest.time}">{$oldest.time}</a> 
  {gt text='by'} {$oldest.user|profilelinkbyuname} 
  <span style="color:#888;font-size:smaller;">{if $oldest.note ne ''}[ {$oldest.note} ]{/if}</span></strong>

  {* $oldest.body is the variable containing the stuff *}
  <br /><br />
  {$oldest.body|transform}
  </div>

  <div class="wiki_footer">
    <div class="inforevision">{gt text='Last edit'}: {$lastedit}<br />
    {gt text='Latest author'}: {$lastauthor|profilelinkbyuname}<br />
    {gt text='Owner'}: {$oldest.owner|profilelinkbyuname}
    </div>
  </div>

</div>
