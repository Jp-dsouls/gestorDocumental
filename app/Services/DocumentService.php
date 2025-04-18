<?php

namespace App\Services;

use App\Repositories\DocumentRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class DocumentService extends BaseService
{
    /**
     * DocumentService constructor.
     *
     * @param DocumentRepository $repository
     */
    public function __construct(DocumentRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Crear un documento con archivo adjunto.
     *
     * @param array $data
     * @param UploadedFile|null $file
     * @return Model
     */
    public function createWithFile(array $data, ?UploadedFile $file = null): Model
    {
        if ($file) {
            $fileData = $this->handleFileUpload($file);
            $data = array_merge($data, $fileData);
        }

        return $this->repository->create($data);
    }

    /**
     * Actualizar un documento con archivo adjunto.
     *
     * @param array $data
     * @param int $id
     * @param UploadedFile|null $file
     * @return bool
     */
    public function updateWithFile(array $data, int $id, ?UploadedFile $file = null): bool
    {
        $document = $this->repository->find($id);

        if ($file) {
            // Eliminar archivo antiguo si existe
            if ($document->file_path) {
                Storage::delete($document->file_path);
            }

            $fileData = $this->handleFileUpload($file);
            $data = array_merge($data, $fileData);
        }

        return $this->repository->update($data, $id);
    }

    /**
     * Manejar la subida de archivos.
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function handleFileUpload(UploadedFile $file): array
    {
        $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $filePath = 'documents/' . date('Y/m/d');
        $fileType = $file->getMimeType();
        $fileSize = $file->getSize();
        $originalName = $file->getClientOriginalName();

        // Si es una imagen, crear thumbnail
        if (Str::startsWith($fileType, 'image/')) {
            $this->createThumbnail($file, $filePath, $fileName);
        }

        // Almacenar el archivo
        $file->storeAs($filePath, $fileName);

        return [
            'file_path' => $filePath . '/' . $fileName,
            'file_name' => $originalName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ];
    }

    /**
     * Crear thumbnail para imágenes.
     *
     * @param UploadedFile $file
     * @param string $path
     * @param string $fileName
     * @return void
     */
    protected function createThumbnail(UploadedFile $file, string $path, string $fileName): void
    {
        $thumbnailPath = $path . '/thumbnails';
        
        // Crear directorio si no existe
        if (!Storage::exists($thumbnailPath)) {
            Storage::makeDirectory($thumbnailPath);
        }

        // Crear thumbnail
        $image = Image::read($file);
        $image->resize(200, 200, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        Storage::put($thumbnailPath . '/' . $fileName, (string) $image->encode());
    }

    /**
     * Eliminar un documento y su archivo asociado.
     *
     * @param int $id
     * @return bool
     */
    public function deleteWithFile(int $id): bool
    {
        $document = $this->repository->find($id);

        if ($document->file_path) {
            // Eliminar archivo principal
            Storage::delete($document->file_path);
            
            // Eliminar thumbnail si es una imagen
            if (Str::startsWith($document->file_type, 'image/')) {
                $thumbnailPath = Str::replaceLast($document->file_name, 'thumbnails/' . $document->file_name, $document->file_path);
                Storage::delete($thumbnailPath);
            }
        }

        return $this->repository->delete($id);
    }

    /**
     * Buscar documentos por término.
     *
     * @param string $term
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $term, int $perPage = 10)
    {
        return $this->repository->search($term, $perPage);
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
        return $this->repository->getByCategory($categoryId, $perPage);
    }

    /**
     * Obtener documentos recientes.
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getRecent(int $limit = 5)
    {
        return $this->repository->getRecent($limit);
    }
} 