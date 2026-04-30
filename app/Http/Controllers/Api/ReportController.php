<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\Wallet;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getSummary(Request $request, string $type)
    {
        $userWalletIds = Wallet::where('user_id', $request->user()->id)->pluck('id');

        $categories = Category::where('type', strtoupper($type))->get();

        $summary = $categories->map(function ($category) use ($request, $userWalletIds) {
            $query = Transaction::where('category_id', $category->id)
                ->whereIn('wallet_id', $userWalletIds);

            if ($request->month) $query->whereMonth('date', $request->month);
            if ($request->year)  $query->whereYear('date', $request->year);

            $amount = $query->sum('amount');

            return ['category' => $category, 'amount' => $amount];
        })->filter(fn($item) => $item['amount'] > 0)->values();

        return $summary;
    }

    public function expenseSummary(Request $request)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Get summary by expense category successful',
            'data'    => ['summary' => $this->getSummary($request, 'EXPENSE')],
        ]);
    }

    public function incomeSummary(Request $request)
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Get summary by income category successful',
            'data'    => ['summary' => $this->getSummary($request, 'INCOME')],
        ]);
    }
}
