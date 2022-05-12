@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <nav class="navbar navbar-expand-lg navbar-light bg-light">
                        <div class="container">
                            <div class="collapse navbar-collapse">
                                <ul class="navbar-nav mb-2 mb-lg-0">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Users
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navUser">
                                            <li><a class="dropdown-item" href="{{route('user.index')}}">List</a></li>
                                            <li><a class="dropdown-item" href="{{route('user.create')}}">Create</a></li>
                                        </ul>
                                    </li>
                                </ul>
                                <ul class="navbar-nav mb-2 mb-lg-0">
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="navPost" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Post
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="navPost">
                                            <li><a class="dropdown-item" href="{{route('post.index')}}">List</a></li>
                                            <li><a class="dropdown-item" href="{{route('post.create')}}">Create</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
