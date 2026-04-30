<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Currency;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::where('user_id', $request->user()->id)
            ->get()
            ->append('balance');

        return response()->json([
            'status'  => 'success',
            'message' => 'Get all wallets successful',
            'data'    => ['wallets' => $wallets],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string',
            'currency_code' => 'required|exists:currencies,code',
        ]);

        $wallet = Wallet::create([
            'user_id'       => $request->user()->id,
            'name'          => $request->name,
            'currency_code' => $request->currency_code,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Wallet added successful',
            'data'    => $wallet,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden access'], 403);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Get detail wallet successful',
            'data'    => $wallet->append('balance'),
        ]);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden access'], 403);
        }

        $request->validate(['name' => 'required|string']);
        $wallet->update(['name' => $request->name]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Wallet updated successful',
            'data'    => $wallet,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $wallet = Wallet::find($id);

        if (!$wallet) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden access'], 403);
        }

        $wallet->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Wallet deleted successful',
        ]);
    }
}
