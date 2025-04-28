<?php

namespace App\Http\Controllers;

class PageController
{
    public function showProfilePage()
    {
        return view('profile.profile');
    }

    public function showImportPage()
    {
        return view('profile.import');
    }

    public function showHomePage()
    {
        return view('home.index');
    }
}
