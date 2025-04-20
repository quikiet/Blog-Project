<?php

namespace App\Http\Controllers;

use App\Models\posts;
use Exception;
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
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', Rules\Password::defaults()],
                'role' => ['sometimes', 'string', 'in:admin,author,user'],
                'avatar' => ['sometimes', 'url', 'max:255'], // Validate là URL
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $this->formatUserResponse($user)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                $e->getMessage()
            ], 500);
        }
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
            'role' => ['sometimes', 'string', 'in:admin,author,user'],
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
    // public function getUserPosts(string $id)
    // {
    //     $user = User::with('user_posts')->findOrFail($id);
    //     return response()->json([
    //         'user' => $this->formatUserResponse($user),
    //         'posts' => $user->user_posts
    //     ]);
    // }

    // app/Http/Controllers/UserController.php
    public function getUserPosts(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $perPage = $request->input('per_page', 10); // Mặc định 10 bài mỗi trang
        $posts = posts::with(['refuses.refuseReason'])
            ->where('user_id', $id)
            ->paginate($perPage);

        return response()->json($posts);
    }
    /**
     * Lấy tất cả bình luận của người dùng
     */
    public function getUserComments($id)
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
    public function getUserPostLikes($id)
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

    public function destroyConfirmPw(Request $request, string $id)
    {
        $userToDelete = User::findOrFail($id);

        // Have you *completely* removed these blocks?
        // if (!Auth::check()) { ... }
        // $currentUser = Auth::user();

        // The password check against the *current* user's password implies authentication
        // If you want to bypass authentication, you might need to rethink this logic entirely.
        // if (!Hash::check($request->input('password'), $currentUser->password)) { ... }

        // If your goal is to delete *any* user with the correct password provided in the request,
        // you would need to fetch the user to be deleted and check the password against *their* password.

        // Example of deleting *any* user with the correct password (without checking logged-in user):
        $passwordFromRequest = $request->input('password');
        if ($passwordFromRequest && Hash::check($passwordFromRequest, $userToDelete->password)) {
            $userToDelete->delete();
            return response()->json(['message' => 'Tài khoản đã được xóa thành công.']);
        } else {
            return response()->json(['message' => 'Mật khẩu không chính xác.', 401]);
        }
    }



}
