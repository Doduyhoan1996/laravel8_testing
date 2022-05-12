@include('user.form', [
    'submit' => __('Add'),
    'url' => route('user.store')
])