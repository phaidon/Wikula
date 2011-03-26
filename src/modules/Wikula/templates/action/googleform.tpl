{* $Id: action/googleform.tpl 41 2008-10-09 18:29:16Z quan $ *}

<form action="{gt text='http://www.google.com/search'}" method="get" name="f">
<div>
  <input type="text" value="{$q}" name="q" size="30" />
  <input name="btnG" type="submit" value="Google" />
</div>
</form>
