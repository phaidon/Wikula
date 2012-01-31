function liveWikulaSearch(input_id)
{
    new Ajax.Autocompleter(input_id, 'wikula_search', Zikula.Config.baseURL + 'ajax.php?module=Wikula&func=search',
        {
            paramName: 'phrase',
            minChars: 1,
            afterUpdateElement: function(data){
                if ($(data).value.substring(0,1) != ' ') {
                    var tag = $(data).value;
                    location.href = Zikula.Config.baseURL+'index.php?module=Wikula&type=user&func=show&tag='+tag;
                } else {
                    location.href = Zikula.Config.baseURL+'index.php?module=Wikula&type=user&func=show&tag=Search&phrase='+phrase;
                }
            }
        }
    );
}
