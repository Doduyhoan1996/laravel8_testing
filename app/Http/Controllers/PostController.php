<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::paginate();
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
        Post::create([
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
        $post = Post::findOrFail($id);
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
        $post = Post::findOrFail($id);
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
            'post' => ['required', 'string', 'unique:posts,post,'.$id ]
        ]);

        $post = Post::findOrFail($id);

        if ($post) {
            if(Gate::denies('post-user', $post)) {
                return redirect()->route('post.index')->with('danger', __('This is not your post'));
            }

            $image = $post->image;
            if ($request->image) {
                $image = ImageHelper::saveImageStorage($request->image, Post::IMAGE_FOLDER);
                ImageHelper::removeImage($post->image, Post::IMAGE_FOLDER);
            }

            $post->post = $request->post;
            $post->image = $image;
            $post->save();

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
        $post = Post::findOrFail($id);

        if ($post) {
            if(Gate::denies('post-user', $post)) {
                return redirect()->route('post.index')->with('danger', __('This is not your post'));
            }

            $post->delete();
            return redirect()->route('post.index')->with('success', __('Delete success'));
        }

        return redirect()->route('post.index')->with('danger', __('Delete fail'));
    }
}
