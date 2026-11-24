<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->user()->hasAnyRole(['admin', 'developer'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended('/');
    }
}