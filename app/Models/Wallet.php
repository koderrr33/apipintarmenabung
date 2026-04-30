<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id', 'name', 'currency_code'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getBalanceAttribute()
    {
        $income = $this->transactions()
            ->whereHas('category', fn($q) => $q->where('type', 'INCOME'))
            ->sum('amount');

        $expense = $this->transactions()
            ->whereHas('category', fn($q) => $q->where('type', 'EXPENSE'))
            ->sum('amount');

        return $income - $expense;
    }
}
