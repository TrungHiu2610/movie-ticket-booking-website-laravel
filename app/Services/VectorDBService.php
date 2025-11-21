<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VectorDBService
{
    
    public function getOrCreateCollection()
    {
        try {
            // Check if movie_embeddings table exists
            return DB::select("SELECT to_regclass('movie_embeddings')")[0]->to_regclass !== null;
        } catch (\Exception $e) {
            Log::error('PostgreSQL collection check error: ' . $e->getMessage());
            return false;
        }
    }

    
    public function addEmbedding($id, $embedding, $text, $metadata = [])
    {
        try {
            // Convert embedding array to PostgreSQL vector format
            $vectorString = '[' . implode(',', $embedding) . ']';

            $movieId = $metadata['movie_id'] ?? null;
            if (!$movieId) {
                Log::error('Missing movie_id in metadata');
                return false;
            }

            // Check if embedding already exists
            $existing = DB::table('movie_embeddings')
                ->where('movie_id', $movieId)
                ->first();

            if ($existing) {
                // Update existing embedding
                DB::table('movie_embeddings')
                    ->where('movie_id', $movieId)
                    ->update([
                        'content' => $text,
                        'embedded_at' => now(),
                        'updated_at' => now()
                    ]);

                // Update vector using raw query
                DB::statement(
                    "UPDATE movie_embeddings SET embedding = ?::vector WHERE movie_id = ?",
                    [$vectorString, $movieId]
                );
            } else {
                // Insert new embedding
                DB::table('movie_embeddings')->insert([
                    'movie_id' => $movieId,
                    'content' => $text,
                    'embedded_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Update vector using raw query
                DB::statement(
                    "UPDATE movie_embeddings SET embedding = ?::vector WHERE movie_id = ?",
                    [$vectorString, $movieId]
                );
            }

            return true;
        } catch (\Exception $e) {
            Log::error('PostgreSQL add embedding error: ' . $e->getMessage());
            return false;
        }
    }

    
    public function query($embedding, $limit = 5, $minSimilarity = 0.3)
    {
        try {
            $vectorString = '[' . implode(',', $embedding) . ']';

            // Use cosine similarity for search with minimum similarity threshold
            $results = DB::select("
                SELECT 
                    id,
                    movie_id,
                    content,
                    1 - (embedding <=> ?::vector) as similarity
                FROM movie_embeddings
                WHERE embedding IS NOT NULL
                    AND (1 - (embedding <=> ?::vector)) >= ?
                ORDER BY embedding <=> ?::vector
                LIMIT ?
            ", [$vectorString, $vectorString, $minSimilarity, $vectorString, $limit]);

            // Format results to match ChromaDB response structure
            // Trả về empty array thay vì null để cho phép fallback
            if (empty($results)) {
                return [
                    'ids' => [[]],
                    'documents' => [[]],
                    'metadatas' => [[]],
                    'distances' => [[]]
                ];
            }

            $ids = [];
            $documents = [];
            $metadatas = [];
            $distances = [];

            foreach ($results as $result) {
                $ids[] = (string)$result->movie_id;
                $documents[] = $result->content;
                $metadatas[] = ['movie_id' => $result->movie_id, 'similarity' => $result->similarity];
                $distances[] = 1 - $result->similarity; // Convert similarity back to distance
            }

            return [
                'ids' => [$ids],
                'documents' => [$documents],
                'metadatas' => [$metadatas],
                'distances' => [$distances]
            ];
        } catch (\Exception $e) {
            Log::error('PostgreSQL query error: ' . $e->getMessage());
            // Trả về empty array thay vì null
            return [
                'ids' => [[]],
                'documents' => [[]],
                'metadatas' => [[]],
                'distances' => [[]]
            ];
        }
    }

    
    public function deleteEmbedding($id)
    {
        try {
            DB::table('movie_embeddings')
                ->where('movie_id', $id)
                ->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('PostgreSQL delete error: ' . $e->getMessage());
            return false;
        }
    }

    
    public function updateEmbedding($id, $embedding, $text, $metadata = [])
    {
        // Just call addEmbedding, it handles both insert and update
        return $this->addEmbedding($id, $embedding, $text, $metadata);
    }
}

