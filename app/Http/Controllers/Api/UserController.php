<?php

namespace App\Http\Controllers\Api;

use App\Dto\Users\CreateUserDto;
use App\Dto\Users\EditUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = $this->userRepository->getAllUsers(
            filter: $request->filter ?? '',
            page: $request->page ?? 1,
            totalPerPage: $request->total_per_page ?? 15
        );

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $user = $this->userRepository->createNew(new CreateUserDto(... $request->validated()));

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$user = $this->userRepository->findById($id)) {
            return response()->json([ 'message' => 'User not found' ], Response::HTTP_NOT_FOUND);
        };

        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        if (!$this->userRepository->update(new EditUserDto(...[$id, ...$request->validated()]))) {
            return response()->json([ 'message' => 'User not found' ], Response::HTTP_NOT_FOUND);
        };

        return response()->json([ 'message' => 'User updated successfully' ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->userRepository->delete($id)) {
            return response()->json([ 'message' => 'User not found' ], Response::HTTP_NOT_FOUND);
        };

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
