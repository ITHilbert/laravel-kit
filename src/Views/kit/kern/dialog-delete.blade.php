{{-- Kern-Theme: Lösch-Dialog ohne Vue; Zustandslogik in js/loesch-dialog.js (initLoeschDialog). Ein Dialog pro Seite. --}}
{{-- route: Lösch-Route, deren letztes Pfadsegment durch die ID des Datensatzes ersetzt wird (z. B. /kunden/0). --}}
@props(['titel' => 'Löschen', 'body' => 'Diesen Eintrag wirklich löschen?', 'route'])
<div class="kit-dialog" data-kit-delete-dialog data-route="{{ $route }}" hidden>
    <div class="kit-dialog__overlay" data-kit-delete-abbrechen></div>
    <div class="kit-dialog__content" role="dialog" aria-modal="true" aria-labelledby="kit-dialog-delete-titel">
        <h2 class="kit-dialog__title" id="kit-dialog-delete-titel">{{ $titel }}</h2>
        <p>{{ $body }}</p>
        <button type="button" class="kit-button" data-variant="cancel" data-kit-delete-abbrechen>Abbrechen</button>
        <button type="button" class="kit-button" data-variant="delete" data-kit-delete-bestaetigen>Löschen</button>
    </div>
</div>
