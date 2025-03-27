<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;



class UserController extends Controller
{
    /**
     * Lấy danh sách tất cả người dùng
     */
    public function getAll()
    {
        $users = User::all();
        return $users->map(function ($user) {
            return $this->formatUserResponse($user);
        });
    }

    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['sometimes', 'string', 'in:admin,editor,user'],
            'avatar' => ['sometimes', 'url', 'max:255'], // Validate là URL
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'] ?? 'user',
            'avatar' => $validated['avatar'] ?? null, // Lưu trực tiếp URL
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $this->formatUserResponse($user)
        ], 201);
    }

    /**
     * Hiển thị thông tin chi tiết người dùng
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return $this->formatUserResponse($user);
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', Rules\Password::defaults()],
            'role' => ['sometimes', 'string', 'in:admin,editor,user'],
            'avatar' => ['sometimes', 'url', 'max:255'], // Validate là URL
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $this->formatUserResponse($user)
        ]);
    }

    /**
     * Xóa người dùng
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Lấy tất cả bài viết của người dùng
     */
    public function getUserPosts(string $id)
    {
        $user = User::with('user_posts')->findOrFail($id);
        return response()->json([
            'user' => $this->formatUserResponse($user),
            'posts' => $user->user_posts
        ]);
    }

    /**
     * Lấy tất cả bình luận của người dùng
     */
    public function getUserComments(string $id)
    {
        $user = User::with('user_comments')->findOrFail($id);
        return response()->json([
            'user' => $this->formatUserResponse($user),
            'comments' => $user->user_comments
        ]);
    }

    /**
     * Lấy tất cả lượt like bài viết của người dùng
     */
    public function getUserPostLikes(string $id)
    {
        $user = User::with('post_likes')->findOrFail($id);
        return response()->json([
            'user' => $this->formatUserResponse($user),
            'likes' => $user->post_likes
        ]);
    }

    /**
     * Format response user
     */
    private function formatUserResponse(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'avatar' => $user->avatar, // Trả về trực tiếp URL ảnh
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
