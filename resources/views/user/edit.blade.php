@include('user.form', [
    'submit' => __('Edit'),
    'url' => route('user.update', $user->id)
])