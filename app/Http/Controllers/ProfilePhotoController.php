<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function show(string $file): Response
    {
        abort_if($file !== basename($file), 404);

        $path = "photos/{$file}";

        abort_unless(Storage::disk('public')->exists($path), 404);

        return response(Storage::disk('public')->get($path), 200, [
            'Content-Type' => Storage::disk('public')->mimeType($path) ?: 'image/jpeg',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
