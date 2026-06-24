<div>
    @if($inhalt)
        {!! $inhalt !!}
    @else
        <iframe src="{{ rtrim(config('laravelkit.legal_text_base_url', 'https://center.it-hilbert.com'), '/') }}/show/datenschutz" width="800px" height="500px"></iframe><br>
        <a href="{{ rtrim(config('laravelkit.legal_text_base_url', 'https://center.it-hilbert.com'), '/') }}/show/datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>
    @endif
</div>
