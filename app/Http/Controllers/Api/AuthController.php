<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInUserRequest;
use App\Http\Requests\SignupUserRequest;
use App\Models\User;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function register(SignupUserRequest $request): JsonResponse
    {
        $validated_fields = $request->safe()->only(['username', 'password']);

        $user = $this->repository->createUser($validated_fields);
        $token = $user->createToken("user-auth'")->plainTextToken;

        $response = [
            'access_token' => $token
        ];

        return response()->json($response);
    }

    public function login(SignInUserRequest $request): JsonResponse
    {
        $validated_fields = $request->safe()->only(['username', 'password']);
        if (!Auth::attempt($validated_fields)) {
            return response()->json([
                'message' => __("auth.failed")
            ], 401);
        }

        $user = $this->repository->selectUser(['username'=> $request['username']]);
        $token = $user->createToken('user-auth')->plainTextToken;

        return response()->json([
            'access_token' => $token
        ]);
    }
}
