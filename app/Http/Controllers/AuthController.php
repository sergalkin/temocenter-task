<?php

namespace App\Http\Controllers;

use App\Auth;
use Illuminate\Http\Request;


class AuthController extends Controller
{

    protected $auth;

    public function __construct()
    {
        $this->auth = new Auth();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        return $this->auth->login($request);
    }
}
