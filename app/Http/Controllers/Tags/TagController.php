<?php

namespace App\Http\Controllers\Tags;

use Spatie\Tags\Tag;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }

    public function fetchall() {
        return Tag::all();
    }
}
