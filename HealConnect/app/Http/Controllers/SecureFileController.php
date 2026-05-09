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

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->check();

        if (!$admin && !$user) {
            abort(403, 'Unauthorized.');
        }

        $isAuthorized = false;

        if ($admin) {
            $isAuthorized = true;
        } elseif (str_starts_with($path, 'chat_files/') || str_starts_with($path, 'voice_messages/')) {
            // Check if user is part of the message and ensure it is an actual file message
            $isAuthorized = \App\Models\Message::where('message', $path)
                ->whereIn('message_type', ['file', 'voice'])
                ->where(function($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                })->exists();
        } elseif (str_starts_with($path, 'valid_ids/') || str_starts_with($path, 'licenses/') || str_starts_with($path, 'business_permits/')) {
            // Check if file belongs to the user's profile
            if ((str_contains($user->valid_id_path ?? '', $path)) || 
                $user->license_path === $path || 
                $user->business_permit_path === $path) {
                $isAuthorized = true;
            }
        } elseif (str_starts_with($path, 'referrals/')) {
            // Check if user is patient or provider for this referral
            $isAuthorized = \App\Models\Appointment::where('referral', $path)
                ->where(function($q) use ($user) {
                    $q->where('patient_id', $user->id)
                      ->orWhere('provider_id', $user->id);
                })->exists();
        }

        if (!$isAuthorized) {
            abort(403, 'Unauthorized access to this protected file.');
        }

        $file = Storage::disk('local')->get($path);
        $type = Storage::disk('local')->mimeType($path);

        $safeMimeTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 
            'application/pdf', 'video/mp4', 'audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/webm'
        ];
        
        $disposition = in_array($type, $safeMimeTypes) ? 'inline' : 'attachment';

        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Content-Disposition', $disposition . '; filename="' . basename($path) . '"');
    }
}
