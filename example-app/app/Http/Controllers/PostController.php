<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function index() {
        // $posts=Post::all();
        $posts=Post::paginate(10);
        return view('post.index', compact('posts'));
    }

    public function create() {
        return view('post.create');
    }

    public function store(Request $request) {

        $validated = $request->validate([
            'title' => 'required|max:20',
            'body' => 'required|max:400',
        ]);

        $validated['user_id'] = auth()->id();

        $post = Post::create($validated);

        $request->session()->flash('message', '保存しました');
        return redirect()->route('post.index');
    }

    public function show($id) {
        $post = Post::find($id);
        return view('post.show', compact('post'));
    }

    public function edit(Request $request, Post $post) {
        if ($post->locked) {
            $request->session()->flash('message', 'ロックされています');
            return redirect()->back();
        }

        return view('post.edit', compact('post'));
    }

    public function update(Request $request, Post $post) {

        $validated = $request->validate([
            'title' => 'required|max:20',
            'body' => 'required|max:400',
        ]);

        $validated['user_id'] = auth()->id();

        $post->update($validated);

        $request->session()->flash('message', '更新しました');
        return redirect()->route('post.show', compact('post'));
    }

    public function destroy(Request $request, Post $post) {
        if ($post->locked) {
            $request->session()->flash('message', 'ロックされています');
            return redirect()->back();
        }

        $post->delete();
        $request->session()->flash('message', '削除しました');
        return redirect()->route('post.index');
    }


    public function lock(Request $request, Post $post)
    {
        $post->locked = !$post->locked;
        $post->save();
        $message = $post->locked ? '投稿をロックしました。' : '投稿のロックを解除しました。';
        $request->session()->flash('message', $message);
        return redirect()->back();
    }

}
