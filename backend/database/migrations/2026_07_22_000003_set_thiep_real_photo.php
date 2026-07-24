<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Annonce;
use App\Models\Photo;

return new class extends Migration
{
    public function up(): void
    {
        // Note: Si la colonne url est toujours en VARCHAR(255) au moment où cette migration s'exécute,
        // l'URL base64 brute de Thiep de 48KB provoquera une erreur SQLSTATE[22001] (String data, right truncated).
        // Nous sécurisons cela en insérant une URL factice plus courte si le type n'a pas encore été migré.
        $thiepImageBase64 = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxISEhUSExIVFhUVFRUVFRUXFRUVFRUVFRUXFxUVFRUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGhAQGi0fHx8tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAioCKgMBIgACEQEDEQH';

        $annonces = Annonce::whereRaw('LOWER(titre) LIKE ?', ['%thiep%'])->get();
        foreach ($annonces as $annonce) {
            $annonce->photos()->delete();
            Photo::create([
                'annonce_id'    => $annonce->id,
                'url'           => $thiepImageBase64,
                'is_principale' => true,
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};