<?php

namespace App\Services;

use App\Events\ExpenseRecorded;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseService
{
    public function record(User $user, array $data, ?UploadedFile $receipt): Expense
    {
        return DB::transaction(function () use ($user, $data, $receipt) {
            $path = $receipt?->store('receipts/'.now()->format('Y/m'), 'local');

            $expense = Expense::create([
                'user_id' => $user->id,
                'category' => $data['category'],
                'description' => $data['description'],
                'amount' => $data['amount'],
                'spent_on' => $data['spent_on'],
                'receipt_path' => $path,
            ]);

            ExpenseRecorded::dispatch($expense);

            return $expense;
        });
    }

    /** Hapus beban sekaligus membatalkan mutasi kas dan berkas nota. */
    public function delete(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $expense->cashMutations()->delete();
            if ($expense->receipt_path) {
                Storage::disk('local')->delete($expense->receipt_path);
            }
            $expense->delete();
        });
    }
}
