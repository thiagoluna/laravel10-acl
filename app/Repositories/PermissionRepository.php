<?php

namespace App\Repositories;

use App\Dto\Permissions\CreatePermissionDto;
use App\Dto\Permissions\EditPermissionDto;
use App\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionRepository
{
    public function __construct(protected Permission $permission)
    {
    }

    /**
     * @param string $filter
     * @param int $page
     * @param int $totalPerPage
     * @return LengthAwarePaginator
     */
    public function getPaginate(string $filter = '', int $page = 1, int $totalPerPage = 15): LengthAwarePaginator
    {
        return $this->permission->where(function ($query) use ($filter) {
            if ($filter != '') {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })
            ->orderBy('id', 'desc')
            ->paginate($totalPerPage, ['*'], 'page', $page);
    }

    /**
     * @param CreatePermissionDto $permissionDto
     * @return Permission     */
    public function createNew(CreatePermissionDto $permissionDto): Permission
    {
        return $this->permission->create((array) $permissionDto);
    }

    /**
     * @param string $id
     * @return Permission|null
     */
    public function findById(string $id): ?Permission
    {
        return $this->permission->find($id);
    }

    /**
     * @param EditPermissionDto $permissionDto
     * @return bool
     */
    public function update(EditPermissionDto $permissionDto): bool
    {
        if (!$permission = $this->findById($permissionDto->id)) {
            return false;
        }

        return $permission->update((array) $permissionDto);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        if (!$permission = $this->findById($id)) {
            return false;
        }

        return $permission->delete();
    }
}
