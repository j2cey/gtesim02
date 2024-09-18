<?php

namespace App\Http\Controllers\Localization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class SetLocaleController extends Controller
{
    public function locale(Request $request)
    {
        // set locale to session
        Session::put('locale', $request->get('locale'));
        $data = ['locale' => $request->get('locale')];

        return response()->json($data);
    }
}
