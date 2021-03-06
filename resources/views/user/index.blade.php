@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between bd-highlight">
                        <div>
                            {{ __('List User') }}
                        </div>
                        <div>
                            @can('check-admin-user')
                            <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">{{ __('Add') }}</a>
                            @endcan
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Email') }}</th>
                                <th scope="col">{{ __('Email Verified At') }}</th>
                                <th scope="col">{{ __('Is Admin') }}</th>
                                <th scope="col">{{ __('Handle') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <th scope="row">{{ $user->id }}</th>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->email_verified_at }}</td>
                                <td>{{ $user->is_admin }}</td>
                                <td>
                                    @can('check-is-user', $user)
                                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('check-admin-user')
                                    <a href="{{ route('user.destroy', $user->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Delete This account?')">{{ __('Delete') }}</a>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{$users->links()}}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
