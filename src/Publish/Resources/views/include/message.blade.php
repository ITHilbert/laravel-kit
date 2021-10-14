@if (isset($message) || $message = Session::get('message'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
    </div>
@endif

@if(isset($errors))
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Error:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ ucfirst($error) }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endif
