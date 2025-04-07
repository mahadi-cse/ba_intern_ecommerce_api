<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $user;
    public function __construct(){
        $this->user = new User();
    }

    public function registration(Request $request){
        if($request->password == $request->retype_password){
            return $this->user->create($request->all());
        }
        else{
            return "password not match";
        }
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function update(Request $request)
    {
        $user = $this->user->find($request->id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $data = $request->except('image'); // exclude image from mass update
    
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
            $data['filename'] = $path;
        }
    
        $user->update($request->all());
        return $user;
    }
    
    public function destroy(Request $request)
    {
        $user = $this->user->find($request->id);
        return $user->delete();
    }
    public function makeAdmin(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'admin_secret' => 'required'
    ]);

    if ($request->admin_secret !== env('ADMIN_CREATION_SECRET')) {
        return response()->json(['error' => 'Invalid admin secret'], 403);
    }

    $user = User::find($request->user_id);
    $user->type = 'admin';
    $user->save();

    return response()->json([
        'message' => 'User promoted to admin successfully',
        'user' => $user
    ]);
}
}
