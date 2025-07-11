<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PredictionController extends Controller
{
    public function index()
    {
        return view('main');
    }

    public function predict(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Maks 5MB
        ]);

        $image = $request->file('image');

        // Kirim ke Flask API tanpa menyimpan gambar
        $response = Http::attach('image', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
            ->post('http://localhost:5000/predict');

        if ($response->failed()) {
            return response()->json(['error' => 'Gagal memproses gambar'], 500);
        }

        $result = $response->json();

        // Ambil confidence dan validasi ambang batas 50%
        $confidence = $result['confidence'] ?? 0;
        if ($confidence < 0.5) {
            return response()->json([
                'class' => null,
                'confidence' => $confidence,
                'message' => 'Gambar tidak bisa diprediksi, mohon upload gambar yang benar.'
            ]);
        }

        return response()->json([
            'class' => $result['class'] ?? 'Tidak diketahui',
            'confidence' => $confidence,
        ]);
    }
}
