<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserRegistered;
use Date;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Laravel\Socialite\Facades\Socialite;
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
            $validationFields = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            $validationFields['role'] = 'user';
            $user = User::create([
                'name' => $validationFields['name'],
                'email' => $validationFields['email'],
                'role' => $validationFields['role'],
                'password' => Hash::make($validationFields['password']),
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

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

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

    public function googleLoginRedirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function googleLoginCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            // dd($googleUser);
            $user = User::where('email', $googleUser->email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'role' => 'user',
                    'email_verified_at' => Date::now(),
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(uniqid()),
                    'oauth_id' => $googleUser->id,
                    'oauth_type' => 'google',
                ]);
                // dd($user);
                $admins = User::where('role', 'admin')->get();
                Notification::send($admins, new NewUserRegistered($user));
                event(new Registered($user));
            }
            // else {
            //     $user->update([
            //         'name' => $googleUser->name,
            //         'avatar' => $googleUser->avatar,
            //     ]);
            // }

            Auth::login($user);

            $token = $user->createToken($user->name, ['*'], now()->addHours(12));

            return redirect()->to(
                env('FRONTEND_URL') . '/login?google=true&token=' . $token->plainTextToken .
                '&user=' . urlencode(json_encode($user)) .
                '&expires_at=' . now()->addHours(12)->timestamp
            );
        } catch (Exception $exception) {
            return redirect()->to(
                // 'http://localhost:4200/login?google=false&error=' . urlencode($exception->getMessage())
                env('FRONTEND_URL') . '/login?google=false&error=' . urlencode($exception->getMessage())
            );
        }
    }
}
