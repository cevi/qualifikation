<?php

namespace App\Exports;

use App\Models\Camp;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class PostsExport implements FromView
{
    /** Ohne Kurs der aktuelle Kurs der angemeldeten Person. */
    public function __construct(private ?Camp $camp = null) {}

    public function view(): View
    {
        return view('exports.posts', [
            'posts' => ($this->camp ?? Auth::user()->camp)->posts,
        ]);
    }
}
