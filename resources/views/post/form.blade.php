@php
use App\Helpers\ImageHelper;
use App\Models\Post;
@endphp

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
                    <form enctype="multipart/form-data" method="POST" action="{{ $url }}">
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
                        <div class="row mb-3">
                            <label for="formFile" class="col-md-1 col-form-label">{{ __('Image') }}</label>
                            <div class="col-md-11">
                                <input accept="image/*" value="{{ old('image', $post->image ) }}" name="image" class="form-control @error('image') is-invalid @enderror" type="file" id="formFile">
                            </div>
                            @error('image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        @if ($post->image)
                        <div class="row mb-3">
                            <img src="{{ ImageHelper::getImage($post->image, Post::IMAGE_FOLDER) }}" class="rounded mx-auto d-block img-thumbnail w-25" alt="...">
                        </div>
                        @endif

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
