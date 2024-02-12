<?php
namespace App\Http\Controllers\Api;
//import Model "Post"
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//import Resource "PostResource"
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;
class PostController extends Controller
{
    /**
    * index
    *
    * @return void
    */
    public function index()
    {
        //get all posts
        $posts = Post::latest()->paginate(5);
        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }
    /**
    * store
    *
    * @param mixed $request
    * @return void
    */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);
        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        //create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        //return response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', 
        $post);
    }
    /**
* destroy
*
* @param mixed $id
* @return void
*/
public function destroy($id)
{
    //find post by id
    $post = Post::find($id);
    //check if post exists
    if ($post) {
        //delete post
        $post->delete();
        //return response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    } else {
        //return response if post not found
        return response()->json(['message' => 'Post not found'], 404);
    }
}

/**
* update
*
* @param mixed $request
* @param mixed $id
* @return void
*/
public function update(Request $request, $id)
{
    //find post by id
    $post = Post::find($id);
    //check if post exists
    if ($post) {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);
        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update post
        $post->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        //return response
        return new PostResource(true, 'Data Post Berhasil Diupdate!', $post);
    } else {
        //return response if post not found
        return response()->json(['message' => 'Post not found'], 404);
    }
}

}