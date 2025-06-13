<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(Trip $trip): Response
    {
        $this->authorize('view', $trip);

        $expenses = $trip->expenses()
            ->with(['payer', 'participants'])
            ->latest()
            ->get();

        return Inertia::render('Expenses/Index', [
            'trip' => $trip,
            'expenses' => $expenses,
            'settlements' => $this->calculateSettlements($trip),
            'participants' => $trip->acceptedParticipants
        ]);
    }

    public function create(Trip $trip): Response
    {
        $this->authorize('create', [Expense::class, $trip]);

        return Inertia::render('Expenses/Create', [
            'trip' => $trip,
            'participants' => $trip->acceptedParticipants
        ]);
    }

    public function store(ExpenseRequest $request, Trip $trip): RedirectResponse
    {
        $this->authorize('create', [Expense::class, $trip]);


        $validated = $request->validated();

        // Create expense
        $expense = $trip->expenses()->create([
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'paid_by' => $validated['paid_by'],
            'split_method' => $validated['split_method'],
        ]);

        // Add participants with their shares
        $totalShares = array_sum($validated['shares']);

        foreach ($validated['shares'] as $userId => $share) {
            $shareAmount = ($share / $totalShares) * $validated['amount'];
            $paidShare = $userId == $validated['paid_by'] ? $shareAmount : 0;
            $owedShare = $userId == $validated['paid_by'] ? 0 : $shareAmount;

            $expense->participants()->attach($userId, [
                'share' => $share,
                'paid_share' => $paidShare,
                'owed_share' => $owedShare
            ]);
        }

        return redirect()->route('trips.expenses.index', $trip);
    }

    private function calculateSettlements(Trip $trip): array
    {
        $balances = [];
        $participants = $trip->acceptedParticipants;

        // Initialize balances
        foreach ($participants as $participant) {
            $balances[$participant->id] = 0;
        }

        // Calculate net balances
        foreach ($trip->expenses as $expense) {
            foreach ($expense->participants as $participant) {
                $balances[$participant->id] += $participant->pivot->paid_share;
                $balances[$participant->id] -= $participant->pivot->owed_share;
            }
        }

        // Simplify debts
        $settlements = [];
        $positive = [];
        $negative = [];

        // Separate into debtors and creditors
        foreach ($balances as $userId => $balance) {
            if ($balance > 0) {
                $positive[] = ['user_id' => $userId, 'amount' => $balance];
            } elseif ($balance < 0) {
                $negative[] = ['user_id' => $userId, 'amount' => abs($balance)];
            }
        }

        // Calculate who owes whom
        foreach ($positive as &$creditor) {
            foreach ($negative as &$debtor) {
                if ($debtor['amount'] > 0 && $creditor['amount'] > 0) {
                    $amount = min($creditor['amount'], $debtor['amount']);

                    $settlements[] = [
                        'from' => $debtor['user_id'],
                        'to' => $creditor['user_id'],
                        'amount' => $amount
                    ];

                    $creditor['amount'] -= $amount;
                    $debtor['amount'] -= $amount;
                }
            }
        }

        return $settlements;
    }
}
