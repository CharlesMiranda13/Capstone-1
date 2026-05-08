<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecureFileController extends Controller
{
    /**
     * Serve secure files (IDs, licenses, referrals, chat files).
     */
    public function show($path)
    {
        $path = urldecode($path);

        if (!\Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        if (!\Auth::guard('admin')->check() && !\Auth::guard('web')->check()) {
            abort(403, 'Unauthorized.');
        }

        $file = \Storage::disk('local')->get($path);
        $type = \Storage::disk('local')->mimeType($path);

        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');
    }
}
