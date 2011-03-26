{* $Id: action/sidenote.tpl 41 2008-10-09 18:29:16Z quan $ *}

<div class="action_sidenote sidenote_{$type}" style="float: {$side}; width: {$width};">
  {if !empty($title)}
  <div class="sidenote_title">
    {$title|safehtml}
  </div>
  {/if}
  <div class="sidenote_text">
    {$text|wakka|pnmodcallhooks:'wikula'}
  </div>
</div>
