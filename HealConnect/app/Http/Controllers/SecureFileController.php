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
        // Decode path if necessary
        $path = urldecode($path);

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        if (!Auth::guard('admin')->check() && !Auth::guard('web')->check()) {
            abort(403, 'Unauthorized.');
        }

        $fullPath = Storage::disk('local')->path($path);

        return response()->file($fullPath);
    }
}
