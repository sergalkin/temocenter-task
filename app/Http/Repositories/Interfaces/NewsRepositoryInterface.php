<?php


namespace App\Http\Repositories\Interfaces;


use App\Http\Repositories\NewsRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection as ResourceCollection;

interface NewsRepositoryInterface
{
    /**
     * @see NewsRepository::get()
     * @param int $id
     * @return mixed
     */
    public function get(int $id);

    /**
     * @see NewsRepository::all()
     * @return ResourceCollection
     */
    public function all(): ResourceCollection;

    /**
     * @see NewsRepository::create()
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse;

    /**
     * @see NewsRepository::update()
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse;

    /**
     * @see NewsRepository::delete()
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse;
}
