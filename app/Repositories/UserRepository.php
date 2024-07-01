<?php

namespace App\Repositories;

use App\Dto\Users\CreateUserDto;
use App\Dto\Users\EditUserDto;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function __construct(protected User $user)
    {
    }

    /**
     * @param string $filter
     * @param int $page
     * @param int $totalPerPage
     * @return LengthAwarePaginator
     */
    public function getAllUsers(string $filter = '', int $page = 1, int $totalPerPage = 15): LengthAwarePaginator
    {
        return $this->user->where(function ($query) use ($filter) {
            if ($filter != '') {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })
            ->with(['permissions'])
            ->orderBy('id', 'desc')
            ->paginate($totalPerPage, ['*'], 'page', $page);
    }

    /**
     * @param CreateUserDto $userDto
     * @return User
     */
    public function createNew(CreateUserDto $userDto): User
    {
        $data = (array) $userDto;
        $data['password'] = bcrypt($data['password']);
        return $this->user->create($data);
    }

    /**
     * @param string $id
     * @return User|null
     */
    public function findById(string $id): ?User
    {
        return $this->user->find($id);
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user->where('email', $email)->first();
    }

    /**
     * @param EditUserDto $userDto
     * @return bool
     */
    public function update(EditUserDto $userDto): bool
    {
        if (!$user = $this->findById($userDto->id)) {
            return false;
        }

        $data = (array) $userDto;
        unset($data['password']);
        if ($userDto->password !== null) {
            $data['password'] = bcrypt($userDto->password);
        }

        return $user->update($data);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        if (!$user = $this->findById($id)) {
            return false;
        }

        return $user->delete();
    }

    /**
     * @param string $id
     * @param array $permissions
     * @return bool|null
     */
    public function syncPermissions(string $id, array $permissions): ?bool
    {
        if (!$user = $this->findById($id)) {
            return null;
        }

        $user->permissions()->sync($permissions);

        return true;
    }

    public function getUserPermissionsById(string $id): \Illuminate\Database\Eloquent\Collection
    {
        return $this->findById($id)->permissions()->get();
    }

    /**
     * @param User $user
     * @param string $permissionName
     * @return bool
     */
    public function hasPermissions(User $user, string $permissionName): bool
    {
        if ($user->isSuperAdmin()) return true;

        return $user->permissions()->where('permissions.name', $permissionName)->exists();
    }
}
