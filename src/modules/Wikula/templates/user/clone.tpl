{include file='user/menu.tpl' tag=$tag}

<div id="wikula">
    <div class="page">
        <p class="z-informationmsg">{gt text='Please fill in a valid target page name and an (optional) edit note.'}</p>
        <form class="z-form" action="{modurl modname='Wikula' type='user' func='clone'}" method="post" enctype="application/x-www-form-urlencoded">
            <div>
                <input type="hidden" name="tag" value="{$tag|safehtml}" />
                <fieldset>
                    <legend>{gt text='Clone'}: <a href="{modurl modname='Wikula' tag=$tag|urlencode}">{$tag|safehtml}</a></legend>
                    <div class="z-formrow">
                        <label for="to">{gt text='Clone as'}</label>
                        <input id="to" type="text" name="to" value="{$to}" size="37" maxlength="75" />
                    </div>
                    <div class="z-formrow">
                        <label for="note">{gt text='Note'}</label>
                        <input id="note" name="note" type="text" value="{$note}" size="37" maxlength="75" />
                    </div>
                    <div class="z-formrow">
                        <input type="checkbox" name="edit" id="editoption"{if $edit} checked="checked"{/if} />
                        <label for="editoption">{gt text='Edit after creation'}</label>
                    </div>
                </fieldset>
                <div class="z-formbuttons z-buttons">
                    <input class="z-bt-ok" name="submit" type="submit" value="{gt text='Submit'}" accesskey="s" />
                    <input class="z-bt-cancel" name="submit" type="submit" value="{gt text='Cancel'}" accesskey="c" />
                </div>
            </div>
        </form>
    </div>
</div>
