{pageaddvar name="javascript" value="prototype"}


<strong><a href="#" onclick="showAllLetters()" title="{gt text='All'}">{gt text='All'}</a></strong>
{foreach item='letter' from=$headerletters}
&nbsp;&nbsp;<strong><a href="#" onclick="showLetter('{$letter}')" title="{$letter|safehtml}">{$letter|safehtml}</a></strong>
{/foreach}
<br /><br />



{assign var='currentchar' value=''}
{foreach name='mypages' item='letter' from=$pagelist key='firstchar'}
    {if $currentchar neq $firstchar}
        {assign var='currentchar' value=$firstchar}
        <div class="letter" id="{$firstchar}">
        <strong>{$firstchar}</strong><br />
    {/if}
    {foreach name='mypagespage' item='page' from=$letter}
        &nbsp;&nbsp;&nbsp;
        <a href="{modurl modname='Wikula' type='user' func='show' tag=$page.tag|urlencode}" title="{$page.tag}">{$page.tag}</a><br />
    {/foreach}
    {if $smarty.foreach.mypages.last ne true}
        <br />
        </div>
    {/if}
{/foreach}

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