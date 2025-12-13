<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display the main transaction overview page.
     */
    public function index(): View
    {
        // Load initial transactions (e.g., last 100)
        $initialTransactions = Transaction::orderBy('transaction_date', 'desc')
            ->limit(100)
            ->get()
            ->map(function (Transaction $tx) {
                return [
                    'id'            => 'TXN-' . $tx->id,
                    'db_id'         => $tx->id, // <<< ADDED: Database ID for API calls
                    'amount'        => (float) $tx->amount,
                    'type'          => $tx->type,
                    'status'        => $tx->status,
                    'date'          => $tx->transaction_date->format('M d, Y'),
                    'recipient'     => [
                        'name'      => $tx->recipient_source,
                        'imageUrl'  => 'https://ui-avatars.com/api/?name=' . urlencode($tx->recipient_source) . '&color=1A202C&background=E2E8F0',
                    ],
                    'description' => $tx->description, 
                ];
            });

        // Calculate summary using DB facade for raw aggregate data
        $summary = DB::table('transactions')
            ->selectRaw('SUM(CASE WHEN type = "Income" AND status = "Completed" THEN amount ELSE 0 END) as totalIncome')
            ->selectRaw('SUM(CASE WHEN type = "Expense" AND status = "Completed" THEN amount ELSE 0 END) as totalExpenses')
            ->first();

        return view('admin.fees.transactions.index', [
            'initialTransactions' => $initialTransactions->toJson(), 
            'totalIncome'         => $summary->totalIncome ?? 0.00,
            'totalExpenses'       => $summary->totalExpenses ?? 0.00,
        ]);
    }

    /**
     * API Endpoint for adding a new transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipientName' => ['required', 'string', 'max:255'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'type'          => ['required', 'in:Income,Expense'],
            'status'        => ['required', 'in:Completed,Pending,Failed'],
            'date'          => ['required', 'date'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        $transaction = Transaction::create([
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'recipient_source' => $validated['recipientName'],
            'status'           => $validated['status'],
            'transaction_date' => $validated['date'],
            'description'      => $validated['description'] ?? null,
            // 'user_id'          => Auth::id(), 
        ]);

        // Return the new transaction formatted for the frontend
        return response()->json([
            'id'        => 'TXN-' . $transaction->id,
            'db_id'     => $transaction->id, // <<< ADDED: DB ID
            'amount'    => (float) $transaction->amount,
            'type'      => $transaction->type,
            'status'    => $transaction->status,
            'date'      => $transaction->transaction_date->format('M d, Y'),
            'recipient' => [
                'name'      => $transaction->recipient_source,
                'imageUrl'  => 'https://ui-avatars.com/api/?name=' . urlencode($transaction->recipient_source) . '&color=1A202C&background=E2E8F0',
            ],
            'description' => $transaction->description, 
        ], 201);
    }

    /**
     * API Endpoint for deleting a transaction.
     */
    public function destroy(Transaction $transaction): JsonResponse
    {
        // Capture old data before deletion for client-side summary update
        $oldAmount = $transaction->amount;
        $oldType = $transaction->type;
        $oldStatus = $transaction->status;

        $transaction->delete();

        // Returning old values allows Alpine to correctly adjust total income/expenses
        return response()->json([
            'status' => 'success',
            'message' => 'Transaction deleted successfully.',
            'old_amount' => (float)$oldAmount,
            'old_type' => $oldType,
            'old_status' => $oldStatus,
        ]);
    }

    /**
     * API Endpoint for updating an existing transaction.
     */
    public function update(Request $request, Transaction $transaction): JsonResponse
    {
        // The transaction object is automatically resolved by Laravel based on the ID in the route

        $validated = $request->validate([
            'recipientName' => ['required', 'string', 'max:255'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'type'          => ['required', 'in:Income,Expense'],
            'status'        => ['required', 'in:Completed,Pending,Failed'],
            'date'          => ['required', 'date'],
            'description'   => ['nullable', 'string', 'max:500'],
        ]);

        // Capture old data before updating for client-side KPI correction
        $oldAmount = (float) $transaction->amount;
        $oldType = $transaction->type;
        $oldStatus = $transaction->status;

        $transaction->update([
            'type'             => $validated['type'],
            'amount'           => $validated['amount'],
            'recipient_source' => $validated['recipientName'],
            'status'           => $validated['status'],
            'transaction_date' => $validated['date'],
            'description'      => $validated['description'] ?? null,
        ]);

        // Return the updated transaction data along with old data for client-side summary calculation
        return response()->json([
            'id'        => 'TXN-' . $transaction->id,
            'db_id'     => $transaction->id,
            'amount'    => (float) $transaction->amount, // New amount
            'type'      => $transaction->type,
            'status'    => $transaction->status,
            // Format date back to 'M d, Y' for the front-end table display
            'date'      => $transaction->transaction_date->format('M d, Y'), 
            'recipient' => [
                'name'      => $transaction->recipient_source,
                'imageUrl'  => 'https://ui-avatars.com/api/?name=' . urlencode($transaction->recipient_source) . '&color=1A202C&background=E2E8F0',
            ],
            'description' => $transaction->description,
            
            // Old values used to reverse previous KPI effect in Alpine.js
            'old_amount' => $oldAmount,
            'old_type' => $oldType,
            'old_status' => $oldStatus,
        ]);
    }
}