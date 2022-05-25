<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Repositories\Eloquent\PostRepository;

class PostController extends Controller
{
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = $this->postRepository->paginate();
        return view('post.index', [
            'posts' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('post.add', [
            'post' => new Post()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'post' => ['required', 'string', 'unique:posts'],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $image = null;
        if ($request->image) {
            $image = ImageHelper::saveImageStorage($request->image, Post::IMAGE_FOLDER);
        }
        $this->postRepository->create([
            'user_id' => Auth::id(),
            'post' => $request->post,
            'image' => $image,
        ]);

        return redirect()->route('post.index')->with('success', __('Create success'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = $this->postRepository->find($id);
        return view('post.show', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = $this->postRepository->find($id);
        Gate::authorize('post-user', $post);
        return view('post.edit', [
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'post' => ['required', 'string', 'unique:posts,post,'.$id ],
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $post = $this->postRepository->find($id);

        if ($post) {
            Gate::authorize('post-user', $post);

            $image = $post->image;
            if ($request->image) {
                $image = ImageHelper::saveImageStorage($request->image, Post::IMAGE_FOLDER);
                ImageHelper::removeImage($post->image, Post::IMAGE_FOLDER);
            }

            $this->postRepository->update([
                'post' => $request->post,
                'image' => $image
            ], $id);

            return redirect()->route('post.index')->with('success', __('Update success'));
        }

        return redirect()->route('post.index')->with('danger', __('Update fail'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = $this->postRepository->find($id);

        if ($post) {
            Gate::authorize('post-user', $post);

            $post->delete();
            return redirect()->route('post.index')->with('success', __('Delete success'));
        }

        return redirect()->route('post.index')->with('danger', __('Delete fail'));
    }
}
