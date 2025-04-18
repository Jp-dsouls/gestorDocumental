<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Obtener todos los registros.
     * 
     * @param array $columns
     * @param array $relations
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Obtener todos los registros paginados.
     * 
     * @param int $perPage
     * @param array $columns
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10, array $columns = ['*'], array $relations = []): LengthAwarePaginator;

    /**
     * Crear un nuevo registro.
     * 
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Actualizar un registro existente.
     * 
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function update(array $data, int $id): bool;

    /**
     * Eliminar un registro.
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Buscar un registro por ID.
     * 
     * @param int $id
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function find(int $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Buscar un registro por campo específico.
     * 
     * @param string $field
     * @param mixed $value
     * @param array $columns
     * @param array $relations
     * @return Model|null
     */
    public function findByField(string $field, $value, array $columns = ['*'], array $relations = []): ?Model;
} 