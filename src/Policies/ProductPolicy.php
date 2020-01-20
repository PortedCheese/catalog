<?php

namespace PortedCheese\Catalog\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use PortedCheese\BaseSettings\Traits\InitPolicy;

class ProductPolicy
{
    use HandlesAuthorization;
    use InitPolicy {
        InitPolicy::__construct as private __ipoConstruct;
    }

    const VIEW_ALL = 2;
    const VIEW = 4;
    const CREATE = 8;
    const UPDATE = 16;
    const DELETE = 32;
    const PUBLISH = 64;
    const CHANGE_CATEGORY = 128;

    public function __construct()
    {
        $this->__ipoConstruct("ProductPolicy");
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
            self::VIEW => "Просмотр",
            self::CREATE => "Добавление",
            self::UPDATE => "Обновление",
            self::DELETE => "Удаление",
            self::PUBLISH => "Публикация",
            self::CHANGE_CATEGORY => "Смена категории",
        ];
    }

    /**
     * Стандартные права.
     *
     * @return int
     */
    public static function defaultRules()
    {
        return self::VIEW_ALL + self::VIEW + self::CREATE + self::UPDATE + self::DELETE + self::PUBLISH + self::CHANGE_CATEGORY;
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
     * Просмотр.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->hasPermission($this->model, self::VIEW);
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

    /**
     * Публикация товара.
     *
     * @param User $user
     * @return bool
     */
    public function publish(User $user)
    {
        return $user->hasPermission($this->model, self::PUBLISH);
    }

    /**
     * Смена категории.
     *
     * @param User $user
     * @return bool
     */
    public function changeCategory(User $user)
    {
        return $user->hasPermission($this->model, self::CHANGE_CATEGORY);
    }
}
