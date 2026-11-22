{{-- Kit-Component kit::kontakt-leiste: feste Leiste mit Telefon, E-Mail und WhatsApp. Themenunabhängig, CSS und SVG inline, kein Script. --}}
@php
    $cfg = (array) config('laravelkit.kontakt_leiste', []);
    $ziffern = static fn ($wert): string => preg_replace('/\D+/', '', (string) $wert) ?? '';

    $telefonZiffern = $ziffern($cfg['telefon'] ?? '');
    $anzeige = trim((string) ($cfg['telefon_anzeige'] ?? '')) ?: (string) ($cfg['telefon'] ?? '');
    $email = trim((string) ($cfg['email'] ?? ''));
    $whatsappZiffern = $ziffern(($cfg['whatsapp'] ?? '') ?: ($cfg['telefon'] ?? ''));
    $whatsappText = (string) ($cfg['whatsapp_text'] ?? '');
    $whatsappUrl = $whatsappZiffern !== ''
        ? 'https://wa.me/'.$whatsappZiffern.($whatsappText !== '' ? '?text='.rawurlencode($whatsappText) : '')
        : '';
    $zeigen = ($cfg['aktiv'] ?? true) && ($telefonZiffern !== '' || $email !== '' || $whatsappUrl !== '');
@endphp
@if ($zeigen)
@once
<style>
:where(.kontakt-leiste){--kontakt-leiste-bg:#1f2937;--kontakt-leiste-text:#fff;--kontakt-leiste-rand:rgba(255,255,255,.28);--kontakt-leiste-font:system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;--kontakt-leiste-top:40vh;--kontakt-leiste-z:1030}
.kontakt-leiste{position:fixed;z-index:var(--kontakt-leiste-z);font-family:var(--kontakt-leiste-font)}
.kontakt-leiste a{display:flex;color:var(--kontakt-leiste-text);background:var(--kontakt-leiste-bg);font-weight:600;line-height:1.2;text-decoration:none}
.kontakt-leiste a:focus-visible{outline:2px solid var(--kontakt-leiste-text);outline-offset:-4px}
.kontakt-leiste svg{width:22px;height:22px;flex-shrink:0}
.kontakt-leiste .lang{display:none}
@media (max-width:767.98px){
.kontakt-leiste{display:grid;grid-auto-flow:column;grid-auto-columns:minmax(0,1fr);left:0;right:0;bottom:0;padding-bottom:env(safe-area-inset-bottom);background:var(--kontakt-leiste-bg);box-shadow:0 -4px 14px rgba(17,24,39,.18)}
.kontakt-leiste a{flex-direction:column;justify-content:center;align-items:center;gap:2px;height:60px;font-size:13px}
.kontakt-leiste a+a{border-left:1px solid var(--kontakt-leiste-rand)}
body{padding-bottom:calc(60px + env(safe-area-inset-bottom))}
}
@media (min-width:768px){
.kontakt-leiste{display:flex;flex-direction:column;gap:4px;top:var(--kontakt-leiste-top);right:0}
.kontakt-leiste a{width:52px;height:52px;position:relative;justify-content:center;align-items:center;border:1px solid var(--kontakt-leiste-rand);border-right:0;border-radius:8px 0 0 8px}
.kontakt-leiste .kurz{display:none}
.kontakt-leiste .lang{display:flex;align-items:center;position:absolute;right:100%;top:-1px;height:52px;padding:0 1rem;white-space:nowrap;font-size:15px;background:var(--kontakt-leiste-bg);color:var(--kontakt-leiste-text);border:1px solid var(--kontakt-leiste-rand);border-right:0;border-radius:8px 0 0 8px;opacity:0;visibility:hidden;pointer-events:none;transform:translateX(12px);transition:opacity .2s ease-out,transform .25s cubic-bezier(.16,1,.3,1),visibility 0s linear .25s}
.kontakt-leiste a:hover .lang,.kontakt-leiste a:focus-visible .lang{opacity:1;visibility:visible;pointer-events:auto;transform:none;transition-delay:0s}
.kontakt-leiste a:hover,.kontakt-leiste a:focus-visible{border-radius:0}
}
@media (prefers-reduced-motion:reduce){
.kontakt-leiste .lang{transition:none;transform:none}
}
@media print{
.kontakt-leiste{display:none!important}
body{padding-bottom:0!important}
}
</style>
@endonce
<nav class="kontakt-leiste" aria-label="Direktkontakt">
@if ($telefonZiffern !== '')
    <a href="tel:+{{ $telefonZiffern }}" aria-label="Anrufen: {{ $anzeige }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <span class="kurz">Anrufen</span>
        <span class="lang">{{ $anzeige }}</span>
    </a>
@endif
@if ($email !== '')
    <a href="mailto:{{ $email }}" aria-label="E-Mail an {{ $email }}">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        <span class="kurz">E-Mail</span>
        <span class="lang">{{ $email }}</span>
    </a>
@endif
@if ($whatsappUrl !== '')
    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Per WhatsApp schreiben (öffnet neues Fenster)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15a8.2 8.2 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24s8.24 3.7 8.24 8.24-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-2-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.23.25-.87.85-.87 2.07s.89 2.4 1.01 2.56c.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.23-.17-.48-.29z"/></svg>
        <span class="kurz">WhatsApp</span>
        <span class="lang">WhatsApp schreiben</span>
    </a>
@endif
</nav>
@endif
