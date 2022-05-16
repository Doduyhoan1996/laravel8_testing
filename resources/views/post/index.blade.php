@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between bd-highlight">
                        <div>
                            {{ __('List Post') }}
                        </div>
                        <div>
                            <a href="{{ route('post.create') }}" class="btn btn-primary btn-sm">{{ __('Add') }}</a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('User Post') }}</th>
                                <th scope="col">{{ __('Post') }}</th>
                                <th scope="col">{{ __('Updated At') }}</th>
                                <th scope="col">{{ __('Created At') }}</th>
                                <th scope="col">{{ __('Handle') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($posts as $post)
                            <tr>
                                <th scope="row">{{ $post->id }}</th>
                                <td>
                                    {{ $post->user->name }}
                                    <br>
                                    {{ $post->user->email }}
                                </td>
                                <td>{{ $post->post }}</td>
                                <td>{{ $post->updated_at }}</td>
                                <td>{{ $post->created_at }}</td>
                                <td>
                                    <a href="{{ route('post.show', $post->id) }}" class="btn btn-info btn-sm">{{ __('Show') }}</a>
                                    @can('post-user', $post)
                                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
                                    <a href="{{ route('post.destroy', $post->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Delete This account?')">{{ __('Delete') }}</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$posts->links()}}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
