<?php

namespace App\Http\Controllers\Api;

use App\Dto\Permissions\CreatePermissionDto;
use App\Dto\Permissions\EditPermissionDto;
use App\Http\Requests\Api\StorePermissionRequest;
use App\Http\Requests\Api\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Repositories\PermissionRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionController
{
    public function __construct(private PermissionRepository $permissionRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $users = $this->permissionRepository->getPaginate(
            filter: $request->filter ?? '',
            page: $request->page ?? 1,
            totalPerPage: $request->total_per_page ?? 15
        );

        return PermissionResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePermissionRequest $request): PermissionResource
    {
        $user = $this->permissionRepository->createNew(new CreatePermissionDto(... $request->validated()));

        return new PermissionResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!$user = $this->permissionRepository->findById($id)) {
            return response()->json([ 'message' => 'Permission not found' ], Response::HTTP_NOT_FOUND);
        };

        return new PermissionResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePermissionRequest $request, string $id)
    {
        if (!$this->permissionRepository->update(new EditPermissionDto(...[$id, ...$request->validated()]))) {
            return response()->json([ 'message' => 'Permission not found' ], Response::HTTP_NOT_FOUND);
        };

        return response()->json([ 'message' => 'Permission updated successfully' ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!$this->permissionRepository->delete($id)) {
            return response()->json([ 'message' => 'Permission not found' ], Response::HTTP_NOT_FOUND);
        };

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
