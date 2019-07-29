<?php


namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

abstract class CoreRepository
{
    /**
     * Создает коллекцию из ресурса
     *
     * @param Collection $collection
     * @param string $resource
     * @return mixed
     */
    protected function returnCollection(Collection $collection, string $resource)
    {
        return new $resource($collection);
    }

    /**
     * Создает пагинированный ресурс
     *
     * @param Paginator $paginator
     * @param string $resource
     * @return mixed
     */
    protected function returnPaginatedCollection(Paginator $paginator, string $resource)
    {
        return new $resource($paginator);
    }

    /**
     * Создаем ресурс
     *
     * @param Model $model
     * @param string $resource
     * @return mixed
     */
    protected function returnResource(Model $model, string $resource)
    {
        return new $resource($model);
    }
}
