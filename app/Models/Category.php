<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Obtener todas las categorías con caché.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllCached()
    {
        return Cache::remember('categories', now()->addHours(24), function () {
            return self::with('children')->whereNull('parent_id')->get();
        });
    }

    /**
     * Relación con documentos.
     *
     * @return HasMany
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relación con categorías hijas.
     *
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relación con categoría padre.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Limpiar caché cuando se actualiza, crea o elimina una categoría.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('categories');
        });

        static::deleted(function () {
            Cache::forget('categories');
        });
    }
} 