<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocumentRequest;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    /**
     * @var DocumentService
     */
    protected $documentService;

    /**
     * DocumentController constructor.
     *
     * @param DocumentService $documentService
     */
    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de documentos.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $categoryId = $request->input('category_id');
            $perPage = $request->input('per_page', 10);

            if ($search) {
                $documents = $this->documentService->search($search, $perPage);
            } elseif ($categoryId) {
                $documents = $this->documentService->getByCategory($categoryId, $perPage);
            } else {
                $documents = $this->documentService->paginate($perPage);
            }

            return view('documents.index', compact('documents'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar documentos: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al cargar los documentos.');
        }
    }

    /**
     * Mostrar formulario de creación.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('documents.create');
    }

    /**
     * Almacenar un nuevo documento.
     *
     * @param DocumentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(DocumentRequest $request)
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();

            $this->documentService->createWithFile($data, $request->file('document'));

            return redirect()->route('documents.index')
                ->with('success', 'Documento creado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear documento: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ha ocurrido un error al crear el documento.');
        }
    }

    /**
     * Mostrar un documento.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $document = $this->documentService->find($id, ['*'], ['category', 'user', 'history']);

            // Verificar si el usuario tiene permiso para ver este documento
            $this->authorize('view', $document);

            return view('documents.show', compact('document'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar documento: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al mostrar el documento.');
        }
    }

    /**
     * Mostrar formulario de edición.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $document = $this->documentService->find($id, ['*'], ['category']);

            // Verificar si el usuario tiene permiso para editar este documento
            $this->authorize('update', $document);

            return view('documents.edit', compact('document'));
        } catch (\Exception $e) {
            Log::error('Error al editar documento: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al editar el documento.');
        }
    }

    /**
     * Actualizar un documento.
     *
     * @param DocumentRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(DocumentRequest $request, $id)
    {
        try {
            $document = $this->documentService->find($id);

            // Verificar si el usuario tiene permiso para actualizar este documento
            $this->authorize('update', $document);

            $data = $request->validated();
            $this->documentService->updateWithFile($data, $id, $request->file('document'));

            return redirect()->route('documents.show', $id)
                ->with('success', 'Documento actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar documento: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Ha ocurrido un error al actualizar el documento.');
        }
    }

    /**
     * Eliminar un documento.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $document = $this->documentService->find($id);

            // Verificar si el usuario tiene permiso para eliminar este documento
            $this->authorize('delete', $document);

            $this->documentService->deleteWithFile($id);

            return redirect()->route('documents.index')
                ->with('success', 'Documento eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar documento: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al eliminar el documento.');
        }
    }

    /**
     * Descargar un documento.
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        try {
            $document = $this->documentService->find($id);

            // Verificar si el usuario tiene permiso para descargar este documento
            $this->authorize('view', $document);

            if (!$document->file_path || !file_exists(storage_path('app/' . $document->file_path))) {
                return back()->with('error', 'El archivo no existe.');
            }

            return response()->download(
                storage_path('app/' . $document->file_path),
                $document->file_name
            );
        } catch (\Exception $e) {
            Log::error('Error al descargar documento: ' . $e->getMessage());
            return back()->with('error', 'Ha ocurrido un error al descargar el documento.');
        }
    }
} 