<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupOpinion;

class OpinionController extends Controller
{
    public function index()
    {
        $opinions = GroupOpinion::with('member')
            ->whereNotNull('opinion')
            ->orWhereNotNull('suggestion')
            ->get();

        return view('admin.opinions.index', compact('opinions'));
    }
}
