<?php

namespace App\Services;

use App\Repositories\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * BaseService constructor.
     * 
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtener todos los registros.
     * 
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->repository->all($columns, $relations);
    }

    /**
     * Obtener todos los registros paginados.
     * 
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10, array $columns = ['*'], array $relations = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $columns, $relations);
    }

    /**
     * Crear un nuevo registro.
     * 
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->repository->create($data);
    }

    /**
     * Actualizar un registro existente.
     * 
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function update(array $data, int $id): bool
    {
        return $this->repository->update($data, $id);
    }

    /**
     * Eliminar un registro.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Buscar un registro por ID.
     * 
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->repository->find($id, $columns, $relations);
    }

    /**
     * Buscar un registro por campo específico.
     * 
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function findByField(string $field, $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->repository->findByField($field, $value, $columns, $relations);
    }
} 