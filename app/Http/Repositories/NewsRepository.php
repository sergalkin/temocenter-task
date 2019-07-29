<?php


namespace App\Http\Repositories;


use App\Http\Repositories\Interfaces\NewsRepositoryInterface as NewsRepositoryInterface;
use App\Http\Resources\News as NewsResource;
use App\Http\Resources\News;
use App\Http\Resources\NewsCollection as NewsCollection;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Http\JsonResponse as JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection as ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;


class NewsRepository extends CoreRepository implements NewsRepositoryInterface
{

    protected $model;

    /**
     * NewsRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Возвращает новость по id в виде ресурса
     *
     * @param int $id
     * @return JsonResponse|NewsResource
     */
    public function get(int $id)
    {
        $news = $this->model->find($id);

        if (!isset($news)) {
            return Response::json(['data' => false]);
        }

        return $this->returnResource($news, NewsResource::class);
    }

    /**
     * Возвращает новости в виде коллекции ресурса
     *
     * @return ResourceCollection
     */
    public function all(): ResourceCollection
    {
        $news = $this->model->orderBy('publication_date', 'desc')->get();
        return $this->returnCollection($news, NewsCollection::class);
    }

    /**
     * Возвращает новости в виде пагинированной коллекции ресурса
     *
     * @param int $elementsPerPage
     * @return ResourceCollection
     */
    public function allWithPaginate(int $elementsPerPage = 15): ResourceCollection
    {
        $news = $this->model->paginate($elementsPerPage);
        return $this->returnPaginatedCollection($news, NewsCollection::class);
    }

    /**
     * Создает новую новость
     *
     * @param array $data
     * @return JsonResponse
     */
    public function create(array $data): JsonResponse
    {
//        if(!array_key_exists('publication_date', $data)){
//            $this->model->publication_date = Carbon::now();
//        };
        if (empty($data)) {
            return Response::json(['data' => false]);
        }

        $news = $this->model->fill($data)->save();
        if (!$news) {
            return Response::json(['message' => 'cant create new news']);
        }
        return Response::json(['data' => $news]);
    }

    /**
     * Обновляет новость
     *
     * @param int $id
     * @param array $data
     * @return JsonResponse
     */
    public function update(int $id, array $data): JsonResponse
    {
        if (empty($data)) {
            return Response::json(['data' => false]);
        }
        $news = $this->model->find($id);

        if (!isset($news)) {
            return Response::json(['message' => 'no such news']);
        }

        $news->fill($data)->save();

        return Response::json(['data' => $news]);
    }

    /**
     * Удаляет новость
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $news = $this->model->find($id);

        if (!isset($news)) {
            return Response::json(['data' => false, 'message' => "news ${id} not found"]);
        }
        return Response::json(['data' => $news->delete()]);

    }
}
