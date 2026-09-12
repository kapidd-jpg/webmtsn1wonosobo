<?php

namespace App\Http\Controllers;

class PortalController extends Controller
{
    public function index()
        {
            if (auth()->check() && auth()->user()->role === 'siswa') {
                auth()->user()->load('extracurriculars');
            }

            return view('home');
        }
}