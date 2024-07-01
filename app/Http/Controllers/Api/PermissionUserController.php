<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionUserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function syncUserPermissions(string $id, Request $request)
    {
        $response = $this->userRepository->syncPermissions($id, $request->permissions);

        if (!$response) {
            return response()->json([ 'message' => 'User not found' ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([ 'message' => 'Ok' ], Response::HTTP_OK);
    }

    public function getUserPermissions(string $id)
    {
        if (!$this->userRepository->findById($id)) {
            return response()->json([ 'message' => 'User not found' ], Response::HTTP_NOT_FOUND);
        };

        $userPermissions = $this->userRepository->getUserPermissionsById($id);

        return PermissionResource::collection($userPermissions);
    }
}
