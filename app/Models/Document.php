<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'category_id',
        'user_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtener documentos con caché.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCached($limit = 20)
    {
        $cacheKey = 'documents_' . $limit;
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($limit) {
            return self::with(['category', 'user'])
                ->latest()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Relación con categoría.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relación con usuario.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con historial de documentos.
     *
     * @return HasMany
     */
    public function history(): HasMany
    {
        return $this->hasMany(DocumentHistory::class);
    }

    /**
     * Crear un historial cuando el documento es modificado.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function ($document) {
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'action' => 'updated',
                'details' => json_encode($document->getChanges()),
            ]);
        });

        static::created(function ($document) {
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'action' => 'created',
                'details' => json_encode($document->toArray()),
            ]);
        });

        static::deleted(function ($document) {
            DocumentHistory::create([
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'action' => 'deleted',
                'details' => json_encode($document->toArray()),
            ]);
        });
    }
} 