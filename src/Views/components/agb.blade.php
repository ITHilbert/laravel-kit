<div>
    @if($inhalt)
        {!! $inhalt !!}
    @else
        <iframe src="{{ rtrim(config('laravelkit.legal_text_base_url', 'https://center.it-hilbert.com'), '/') }}/show/agb" width="800px" height="500px"></iframe><br>
        <a href="{{ rtrim(config('laravelkit.legal_text_base_url', 'https://center.it-hilbert.com'), '/') }}/show/agb" target="_blank">AGBs in neuem Fenster öffnen</a>
    @endif
</div>
