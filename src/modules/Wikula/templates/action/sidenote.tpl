<div class="action_sidenote sidenote_{$type}" style="float: {$side}; width: {$width};">
  {if !empty($title)}
  <div class="sidenote_title">
    {$title|safehtml}
  </div>
  {/if}
  <div class="sidenote_text">
    {$text|notifyfilters:'wikula.filter_hooks.body.filter'}
  </div>
</div>
