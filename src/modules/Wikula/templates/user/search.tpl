{if $modvars.Wikula.ajaxsearch}   
    {ajaxheader modname=$modinfo.name filename='ajaxsearch.js' ui=true}
    {pageaddvar name='stylesheet' value='modules/Wikula/style/ajaxsearch.css'}
    <div id="wikula_search"></div>
    <script type="text/javascript">
        liveWikulaSearch('wikula_phrase');
    </script>
{/if}
