<div>
    @if($datenschutzInhalt)
        {!! $datenschutzInhalt['content'] !!}
    @else
        <iframe src="https://center.it-hilbert.com/show/datenschutz" width="800px" height="500px"></iframe><br>
        <a href="https://center.it-hilbert.com/show/datenschutz" target="_blank">Datenschutzerklärung in neuem Fenster öffnen</a>
    @endif
</div>
