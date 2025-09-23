<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user->is_verified_by_admin || $user->status !== 'Active') {
            return redirect()->route('account.pending');
        }

        return $next($request);
    }
}
