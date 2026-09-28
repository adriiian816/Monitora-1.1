<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

class SingleDeviceSession extends BaseAuthenticateSession
{
    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function logout($request)
    {
        $this->guard()->logoutCurrentDevice();

        $request->session()->flush();
        $request->session()->regenerate();

        $request->session()->flash('status', 'Akun Anda telah login di perangkat lain sehingga sesi Anda diakhiri.');

        throw new AuthenticationException(
            'Unauthenticated.', [$this->auth->getDefaultDriver()], $this->redirectTo($request)
        );
    }
}
