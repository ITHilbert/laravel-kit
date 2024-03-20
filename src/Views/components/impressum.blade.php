<div>
    @if($impressumInhalt)
        {!! $impressumInhalt['content'] !!}
    @else
        <iframe src="https://center.it-hilbert.com/show/impressum" width="800px" height="500px"></iframe><br>
        <a href="https://center.it-hilbert.com/show/impressum" target="_blank">Impressum in neuem Fenster öffnen</a>
    @endif
</div>
