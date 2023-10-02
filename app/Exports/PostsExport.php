<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class PostsExport implements FromView
{
    public function view(): View
    {
        return view('exports.posts', [
            'posts' => Auth::user()->camp->posts
        ]);
    }
}
