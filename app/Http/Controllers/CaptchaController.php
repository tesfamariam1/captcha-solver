<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;

class CaptchaController extends Controller
{
    public function solve(Request $request)
    {
// Validate URL
        $this->validate($request, ['url' => 'required|url']);

// Generate file name
        $fileName = Str::random(12).'.png';

// Download image
        $context = stream_context_create(['ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]]);

        $imageData = file_get_contents($request->url, false, $context);

// Save to storage
        Storage::put('public/uploads/'.$fileName, $imageData);

// Path to image
        $imagePath = storage_path('app/public/uploads/'.$fileName);

// OCR
        $ocr = new TesseractOCR();
        $ocr->image($imagePath);
        $ocr->lang('eng','jpn','spa');
        $text = preg_replace('/\s+/', ' ', $ocr->run());
// Delete image after OCR
        Storage::delete('public/uploads/'.$fileName);
        return response()->json([
            "success" => true,
            "response" => $text
        ]);

    }
}

//$image = Storage::path('public/upload/text.png');
//echo (new TesseractOCR($image))
//    ->lang('eng', 'jpn', 'spa')
//    ->run();
