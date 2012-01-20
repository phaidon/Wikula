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
{foreach name='mypages' item='tags' from=$pagelist key='firstchar'}

    
    <dl class="letter" id="{$firstchar}">
        <dt><strong>{$firstchar}</strong></dt>
        {foreach name='mypagespage' item='tag' from=$tags}
        <dd>
            <a href="{modurl modname='Wikula' type='user' func='show' tag=$tag|urlencode}" title="{$tag|safetext}">{$tag|safetext}</a>
        </dd>
        {/foreach}
    </dl>

{/foreach}