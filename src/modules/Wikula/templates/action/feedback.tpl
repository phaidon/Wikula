{* $Id: action/feedback.tpl 41 2008-10-09 18:29:16Z quan $ *}

<blockquote>
  {if $success}
    <p>{gt text='Thank you! Message sent.'}</p>
  {else}
    {if $error eq 1}
      <p>{gt text='Please Enter a name!'}</p>
    {elseif $error eq 2}
      <p>{gt text='Please enter a valid e-mail address!'}</p>
    {elseif $error eq 3}
      <p>{gt text='Please enter a comment!'}</p>
    {/if}
    <p>{gt text='Fill in the form below to send us your comments:'}</p>
    <form action="{modurl modname='wikula' type='user' func='main' tag=$tag|urlencode}" method="post" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="mail" value="result" />
    <div>
      <div class="pn-userformrow" style="vertical-align:top;">
        <label for="wikula_name">{gt text='Name'}</label><br />
        <input id="wikula_name" name="name" value="{$name}" type="text" />
      </div>
      <div class="pn-userformrow" style="vertical-align:top;">
        <label for="wikula_email">{gt text='E-mail address'}</label><br />
        <input id="wikula_email" name="email" value="{$email}" type="text" />
      </div>
      <div class="pn-userformrow" style="vertical-align:top;">
        <label for="wikula_comments">{gt text='Your comments'}</label><br />
        <textarea id="wikula_comments" name="comments" rows="15" cols="40">{$comments}</textarea>
      </div>
      <div class="pn-userformrow" style="text-align:left;"><br />
        <input name="submit" type="submit" value="{gt text='Submit'}" />
      </div>
      <div style="clear:both"></div>
    </div>
    </form>
  {/if}
</blockquote>
