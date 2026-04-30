<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function index(Request $request)
    {
        $currencies = Currency::orderBy('code')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all currencies successful',
            'data' => ['currencies' => $currencies],
        ]);
    }
}
