<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Notification;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
            ]);

            $admins = User::where('role', 'admin')->get();

            Notification::send($admins, new NewUserRegistered($user));

            $token = $user->createToken($request->name);

            return response()->json([
                'message' => 'Tạo người dùng thành công!',
                'user' => $user,
                'token' => $token->plainTextToken
            ], 201);
        } catch (Exception $exception) {
            return response()->json(["error" => $exception->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => "Mật khẩu không chính xác",
            ], 401);
        }

        $token = $user->createToken($user->name, ['*'], now()->addHours(12));

        return response()->json([
            'user' => $user,
            'token' => $token->plainTextToken,
            'expires_at' => now()->addHours(12)->timestamp,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Logged out',
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}
