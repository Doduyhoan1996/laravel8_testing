<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="flash-message">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(Session::has($msg))
                        <p class="alert alert-{{ $msg }}" role="alert">
                            {{ Session::get($msg) }}
                        </p>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>