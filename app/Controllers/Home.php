<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        if (session()->get('client_session')) {
            return redirect()->to('/client/dashboard');
        }

        return view('auth/login');
    }
}
