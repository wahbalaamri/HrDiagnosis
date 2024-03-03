<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DemoUsersController extends Controller
{
    //
    //index function to show registration form for new demo users
    public function index()
    {
        return view('demoUsers.index');
    }
    function store(Request $request)  {
        dd($request->all());
    }
}
