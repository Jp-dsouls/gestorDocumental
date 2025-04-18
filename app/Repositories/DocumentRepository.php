<?php

namespace App\Repositories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DocumentRepository extends BaseRepository implements RepositoryInterface
{
    /**
     * DocumentRepository constructor.
     *
     * @param Document $model
     */
    public function __construct(Document $model)
    {
        parent::__construct($model);
    }

    /**
     * Buscar documentos por título o descripción.
     *
     * @param string $term
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 10)
    {
        return $this->model
            ->where('title', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%")
            ->with(['category', 'user'])
            ->paginate($perPage);
    }

    /**
     * Obtener documentos por categoría.
     *
     * @param int $categoryId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10)
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->with(['category', 'user'])
            ->paginate($perPage);
    }

    /**
     * Obtener documentos por usuario.
     *
     * @param int $userId
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getByUser(int $userId, int $perPage = 10)
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['category', 'user'])
            ->paginate($perPage);
    }

    /**
     * Obtener documentos recientes con caché.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecent(int $limit = 5): Collection
    {
        $cacheKey = 'documents_recent_' . $limit;
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($limit) {
            return $this->model
                ->with(['category', 'user'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Actualizar un documento y su historial.
     *
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function update(array $data, int $id): bool
    {
        $document = $this->find($id);
        $updated = $document->update($data);
        
        // Limpiar caché
        $this->clearCache();
        
        return $updated;
    }

    /**
     * Crear un documento y registrar su historial.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $document = $this->model->create($data);
        
        // Limpiar caché
        $this->clearCache();
        
        return $document;
    }

    /**
     * Eliminar un documento y registrar su historial.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $document = $this->find($id);
        $deleted = $document->delete();
        
        // Limpiar caché
        $this->clearCache();
        
        return $deleted;
    }

    /**
     * Limpiar caché relacionada con documentos.
     */
    private function clearCache(): void
    {
        Cache::forget('documents_recent_5');
        Cache::forget('documents_recent_10');
        Cache::forget('documents_20');
    }
} 