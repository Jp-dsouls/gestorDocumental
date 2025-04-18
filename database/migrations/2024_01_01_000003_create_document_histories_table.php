<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action');
            $table->json('details')->nullable();
            $table->timestamps();
            
            // Particionamiento por mes para tablas grandes (requiere MySQL 8.0+)
            // $table->timestamp('created_at')->useCurrent()->index();
            // $table->partitionBy('RANGE', DB::raw('UNIX_TIMESTAMP(created_at)'), 
            //     [
            //         ['partition p0 values less than (UNIX_TIMESTAMP("2023-01-01 00:00:00"))'],
            //         ['partition p1 values less than (UNIX_TIMESTAMP("2023-02-01 00:00:00"))'],
            //         ['partition p2 values less than (UNIX_TIMESTAMP("2023-03-01 00:00:00"))'],
            //         // Agregar más particiones según sea necesario
            //         ['partition pmax values less than MAXVALUE'],
            //     ]
            // );
            
            // Índices para mejorar el rendimiento
            $table->index('document_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_histories');
    }
}; 