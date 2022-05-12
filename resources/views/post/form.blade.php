@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a class="mr-3" href="{{route('post.index')}}">{{ __('List Post') }}</a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ $url }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="post" class="col-md-1 col-form-label">{{ __('Post') }}</label>

                            <div class="col-md-11">
                                <textarea id="post" class="form-control @error('post') is-invalid @enderror" name="post" required autocomplete="post" autofocus>{{ old('post', $post->post ) }}</textarea>

                                @error('post')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="text-md-end">
                                <button type="submit" class="btn btn-primary">
                                    {{ $submit }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
