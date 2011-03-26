{* $Id: action/lastedit.tpl 41 2008-10-09 18:29:16Z quan $ *}

{if $action_le.page}
  <div class="lastedit">
    {gt text='Last edited by '}
    <strong>{$action_le.page.user|safehtml|userprofilelink}</strong>
    {if $action_le.show > 0}&nbsp;<span class="pagenote">[&nbsp;{$action_le.page.note}&nbsp;]</span>{/if}
    {if $action_le.show > 1}<br />{$action_le.dateformatted}{/if}
    {if $action_le.show > 2}{$action_le.timeformatted} DIFFLINK{/if}
  </div>
{/if}
