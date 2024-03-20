<div>
    @if($cookieInhalt)
        {!! $cookieInhalt['content'] !!}
    @else
        <iframe src="https://center.it-hilbert.com/show/cookie-richtlinie" width="800px" height="500px"></iframe><br>
        <a href="https://center.it-hilbert.com/show/cookie-richtlinie" target="_blank">Cookie-Richtlinie in neuem Fenster öffnen</a>
    @endif
</div>
