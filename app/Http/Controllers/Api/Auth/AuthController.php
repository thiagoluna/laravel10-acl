<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * @param AuthRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function auth(AuthRequest $request): JsonResponse
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        return response()->json([ 'token' => $user->createToken($request->device_name)->plainTextToken ]);
    }

    /**
     * @return UserResource
     */
    public function me(): UserResource
    {
        $user = Auth::user();
        $user->load('permissions');

        return new UserResource($user);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->noContent();
    }
}
