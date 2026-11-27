{{-- Kit-Component kit::dialog-delete: Dialog aus dem Template des konfigurierten Themes, danach das Formular #formDelete (für alle Themes gleich). --}}
{{-- Die action setzt das JS beim Bestätigen (js/loesch-dialog.js bzw. die Vue-Insel). --}}
@include(\ITHilbert\LaravelKit\Kit\KitView::fuer('dialog-delete'))
<form id="formDelete" method="POST">
    @csrf
    @method('DELETE')
</form>
