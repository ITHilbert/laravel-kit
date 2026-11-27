{{-- Eingefrorenes Theme: Lösch-Dialog als Vue-Insel dialog-delete (Props titel, body, route); das Formular #formDelete gibt die Component aus. --}}
@props(['titel' => 'Löschen', 'body' => 'Diesen Eintrag wirklich löschen?', 'route'])
<div data-vue="dialog-delete" data-props="{{ json_encode(['titel' => $titel, 'body' => $body, 'route' => $route], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"></div>
