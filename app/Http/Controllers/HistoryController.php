<?php

namespace App\Http\Controllers;

class HistoryController extends Controller
{
    public function suhu()
    {
        return view('history.suhu');
    }

    public function kelembapan()
    {
        return view('history.kelembapan');
    }

    public function ph()
    {
        return view('history.ph');
    }

    public function debit()
    {
        return view('history.debit');
    }
}
