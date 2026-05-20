<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class UserAgreementController extends Controller
{
    public function download(): BinaryFileResponse
    {
        $path = public_path('documents/user-agreement.txt');

        if (!File::exists($path)) {
            abort(404, 'Файл пользовательского соглашения не найден');
        }

        return response()->download(
            $path,
            'user-agreement-freeland.txt',
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }
}
