<?php

namespace PortedCheese\Catalog\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use PortedCheese\BaseSettings\Traits\InitPolicy;

class ProductVariationPolicy
{
    use HandlesAuthorization;
    use InitPolicy {
        InitPolicy::__construct as private __ipoConstruct;
    }

    const VIEW_ALL = 2;
    const CREATE = 4;
    const UPDATE = 8;
    const DELETE = 16;

    public function __construct()
    {
        $this->__ipoConstruct("ProductVariationPolicy");
    }

    /**
     * Получить права доступа.
     *
     * @return array
     */
    public static function getPermissions()
    {
        return [
            self::VIEW_ALL => "Просмотр всех",
            self::CREATE => "Добавление",
            self::UPDATE => "Обновление",
            self::DELETE => "Удаление",
        ];
    }

    /**
     * Стандартные права.
     *
     * @return int
     */
    public static function defaultRules()
    {
        return self::VIEW_ALL + self::CREATE + self::UPDATE + self::DELETE;
    }

    /**
     * Determine whether the user can view any tasks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission($this->model, self::VIEW_ALL);
    }

    /**
     * Добавление.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermission($this->model, self::CREATE);
    }

    /**
     * Обновление.
     *
     * @param User $user
     * @return bool
     */
    public function update(User $user)
    {
        return $user->hasPermission($this->model, self::UPDATE);
    }

    /**
     * Удаление.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->hasPermission($this->model, self::DELETE);
    }
}
