@include('post.form', [
    'submit' => __('Edit'),
    'url' => route('post.update', $post->id)
])