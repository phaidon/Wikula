{ajaxheader modname='Wikula'}
{strip}
{pageaddvarblock}
<script type="text/javascript">
    function showAllLetters() {

        $$('.letter').each(
        function(s) {
            s.show();
        }
        );
    }

    function showLetter(letter) {

        $$('.letter').each(
        function(s) {
            if(s.id != letter) {
                s.hide();
            } else {
                s.show();
            }
        }
        );
    }
</script>
{/pageaddvarblock}
{/strip}

<p class="z-bold">
    <a href="#" onclick="showAllLetters()" title="{gt text='All'}">{gt text='All'}</a>
    {foreach item='letter' from=$headerletters}
    &nbsp;&nbsp;
    <a href="#" onclick="showLetter('{$letter}')" title="{$letter|safehtml}">{$letter|safehtml}</a>
    {/foreach}
</p>

{assign var='currentchar' value=''}
{foreach name='mypages' item='letter' from=$pagelist key='firstchar'}

    {if $currentchar neq $firstchar}
    {assign var='currentchar' value=$firstchar}
    <dl class="letter" id="{$firstchar}">
    <dt><strong>{$firstchar}</strong></dt>
    {/if}

        {foreach name='mypagespage' item='page' from=$letter}
        <dd>
            <a href="{modurl modname='Wikula' type='user' func='show' tag=$page.tag|urlencode}" title="{$page.tag|safetext}">{$page.tag|safetext}</a>
        </dd>
        {/foreach}

    {if $smarty.foreach.mypages.last ne true}
    </dl>
    {/if}

{/foreach}