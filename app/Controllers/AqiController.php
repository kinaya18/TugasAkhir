<?php

namespace App\Controllers;

class AqiController extends BaseController
{
    public function index()
    {
        return view('InfoAqi/info-aqi');
    }
}