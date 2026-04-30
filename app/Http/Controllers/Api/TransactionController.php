<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page'     => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1',
            'month'    => 'nullable|integer|between:1,12',
            'year'     => 'nullable|integer',
        ]);

        $userWalletIds = Wallet::where('user_id', $request->user()->id)->pluck('id');

        $query = Transaction::with(['wallet', 'category'])
            ->whereIn('wallet_id', $userWalletIds)
            ->orderBy('date', 'desc');

        if ($request->month) {
            $query->whereMonth('date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('date', $request->year);
        }

        $perPage = $request->per_page ?? 25;
        $result  = $query->paginate($perPage);

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_id'   => 'required|exists:wallets,id',
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|integer|min:1',
            'date'        => 'required|date_format:Y-m-d',
            'note'        => 'nullable|string',
        ]);

        $wallet = Wallet::find($request->wallet_id);

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden access'], 403);
        }

        $transaction = Transaction::create($request->only([
            'wallet_id', 'category_id', 'amount', 'date', 'note'
        ]));

        return response()->json([
            'status'  => 'success',
            'message' => 'Transaction added successful',
            'data'    => $transaction,
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $wallet = Wallet::find($transaction->wallet_id);

        if ($wallet->user_id !== $request->user()->id) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden access'], 403);
        }

        $transaction->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Transaction deleted successful',
        ]);
    }
}
