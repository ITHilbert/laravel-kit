
@if(isset($breadcrumb))
<breadcrumb>
    @foreach($breadcrumb->getItems() as $bc )
        <breadcrumb-item href="{{ $bc['url'] }}">{{ $bc['title'] }}</breadcrumb-item>
    @endforeach
</breadcrumb>
@endif
