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
                    <div class="row mb-3">
                        <label for="post" class="col-md-1 col-form-label fw-bold">{{ __('Post') }}</label>

                        <div class="col-md-11 col-form-label">
                            {{ $post->post }}
                        </div>
                    </div>
                    @if ($post->image)
                    <div class="row mb-3">
                        <img src="{{ ImageHelper::getImage($post->image, Post::IMAGE_FOLDER) }}" class="rounded mx-auto d-block img-thumbnail w-25" alt="...">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
