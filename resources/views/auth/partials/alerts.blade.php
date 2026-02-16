@if (session('status'))
    <div class="alert alert-success small" role="alert">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger small" role="alert">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
