{* $Id: wikula_user_backlinks.tpl 80 2008-12-16 11:03:25Z mateo $ *}
{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
  <div class="page">

    <form action="{modurl modname='wikula' type='user' func='clone'}" method="post" enctype="application/x-www-form-urlencoded">
    <div id="wikula_cloneform">
      {modurl modname='wikula' tag=$tag|urlencode assign='pageurl'}
      <h3>{gt text='Clone <a href="%1$s">%1$s</a>' tag2=$tag|safehtml tag1=$pageurl}</h3>
      <input type="hidden" name="tag" value="{$tag|safehtml}" />

      <table class="clone">
        <tr><td colspan="2">{gt text='Please fill in a valid target page name and an (optional) edit note.'}</td></tr>
        <tr>
          <td><label for="to">{gt text='Clone as'}:</label></td>
          <td><input id="to" type="text" name="to" value="{$to}" size="37" maxlength="75" /></td>
        </tr>
        <tr>
          <td><label for="note">{gt text='Note'}</label></td>
          <td><input id="note" name="note" type="text" value="{$note}" size="37" maxlength="75" /></td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input type="checkbox" name="edit" id="editoption"{if $edit} checked="checked"{/if} />
            <label for="editoption">{gt text='Edit after creation'}</label>
          </td>
        </tr>
        <tr>
          <td></td>
          <td>
            <input name="submit" type="submit" value="{gt text='Submit'}" accesskey="s" />&nbsp;
            <input name="submit" type="submit" value="{gt text='Cancel'}" accesskey="c" />
          </td>
        </tr>
      </table>
    </div>
    </form>

  </div>
</div>

<div class="clear">&nbsp;</div>
{*include file='wikula_user_footer.tpl'*}
