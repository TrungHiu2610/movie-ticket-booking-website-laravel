<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enable pgvector extension (Supabase has this by default)
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        // Create table
        Schema::create('movie_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->timestamp('embedded_at');
            $table->timestamps();
        });

        // Thêm cột vector riêng
        DB::statement('ALTER TABLE movie_embeddings ADD COLUMN embedding vector(768)');

        // Tạo index IVFFlat cho vector
        DB::statement('CREATE INDEX movie_embeddings_embedding_idx 
                   ON movie_embeddings 
                   USING ivfflat (embedding vector_cosine_ops) 
                   WITH (lists = 100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_embeddings');
    }
};
