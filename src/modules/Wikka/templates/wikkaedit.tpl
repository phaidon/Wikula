{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit_data.js"}
{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit_search.js"}
{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit.js"}
{pageaddvar name="stylesheet" value="modules/Wikka/editor/wikkaedit.css"}

<script type="text/javascript">
    // ===== run wikkaedit =====
    var textareas = document.getElementsByTagName('textarea');

    if (textareas.length > 0) {
        var varWikkaEdit = new WikkaEdit(document.getElementById(textareas[0].id));
        //if (varWikkaEdit.browserSupported())
        varWikkaEdit.init();
    }
</script>