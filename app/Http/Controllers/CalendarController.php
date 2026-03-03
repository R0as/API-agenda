<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $events = \App\Models\Event::all();
        return view('calendar', compact('events'));
    }
}
