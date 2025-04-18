<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    /**
     * Determinar si el usuario puede ver cualquier documento.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true; // Todos los usuarios autenticados pueden ver la lista de documentos
    }

    /**
     * Determinar si el usuario puede ver el documento.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Document $document)
    {
        // El usuario puede ver sus propios documentos o si tiene permisos especiales
        if ($document->user_id === $user->id) {
            return true;
        }

        // También permitir a administradores o usuarios con roles específicos
        return $user->hasPermissionTo('view documents') || $user->hasRole('admin');
    }

    /**
     * Determinar si el usuario puede crear documentos.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // Cualquier usuario autenticado puede crear documentos
        return true;
    }

    /**
     * Determinar si el usuario puede actualizar el documento.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Document $document)
    {
        // El usuario puede actualizar sus propios documentos
        if ($document->user_id === $user->id) {
            return true;
        }

        // También permitir a administradores o usuarios con roles específicos
        return $user->hasPermissionTo('edit documents') || $user->hasRole('admin');
    }

    /**
     * Determinar si el usuario puede eliminar el documento.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Document $document)
    {
        // El usuario puede eliminar sus propios documentos
        if ($document->user_id === $user->id) {
            return true;
        }

        // También permitir a administradores o usuarios con roles específicos
        return $user->hasPermissionTo('delete documents') || $user->hasRole('admin');
    }

    /**
     * Determinar si el usuario puede restaurar el documento.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Document $document)
    {
        // Solo administradores pueden restaurar documentos eliminados
        return $user->hasPermissionTo('restore documents') || $user->hasRole('admin');
    }

    /**
     * Determinar si el usuario puede eliminar permanentemente el documento.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Document $document)
    {
        // Solo administradores pueden eliminar permanentemente documentos
        return $user->hasRole('admin');
    }
} 