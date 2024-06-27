<?php

namespace App\Repositories;

use App\Dto\Users\CreateUserDto;
use App\Dto\Users\EditUserDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function __construct(protected User $user)
    {
    }

    public function getAllUsers(string $filter = '', int $page = 1, int $totalPerPage = 15): LengthAwarePaginator
    {
        return $this->user->where(function ($query) use ($filter) {
            if ($filter != '') {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })
            ->orderBy('id', 'desc')
            ->paginate($totalPerPage, ['*'], 'page', $page);
    }

    public function createNew(CreateUserDto $userDto): User
    {
        $data = (array) $userDto;
        $data['password'] = bcrypt($data['password']);
        return $this->user->create($data);
    }

    public function findById(string $id): ?User
    {
        return $this->user->find($id);
    }

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

    public function delete(string $id): bool
    {
        if (!$user = $this->findById($id)) {
            return false;
        }

        return $user->delete();
    }
}
