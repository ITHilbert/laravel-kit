<div>
    @if($agbInhalt)
        {!! $agbInhalt['content'] !!}
    @else
        <iframe src="https://center.it-hilbert.com/show/agb" width="800px" height="500px"></iframe>
        <a href="https://center.it-hilbert.com/show/agb" target="_blank">AGBs in neuem Fenster öffnen</a>
    @endif
</div>
