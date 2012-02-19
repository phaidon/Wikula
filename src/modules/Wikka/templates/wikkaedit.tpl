{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit_data.js"}
{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit_search.js"}
{pageaddvar name="javascript" value="modules/Wikka/editor/wikkaedit.js"}
{pageaddvar name="stylesheet" value="modules/Wikka/editor/wikkaedit.css"}


<script type="text/javascript">
    // ===== run wikkaedit =====
    var varWikkaEdit = new WikkaEdit(document.getElementById("{{$textfieldname}}"));
    //if (varWikkaEdit.browserSupported())
    varWikkaEdit.init();
</script>


