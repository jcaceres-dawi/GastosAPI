<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = $request->user()->expenses;

        return response()->json([
            'data' => $expenses,
            'success' => true,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string|in:comida,ocio,electronica,utilidades,ropa,salud,otros',
        ]);

        $expense = $request->user()->expenses()->create($data, [
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Expense created successfully',
            'data' => $expense,
            'success' => true,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'amount' => 'required|numeric',
            'description' => 'required|string',
            'category' => 'required|string|in:comida,ocio,electronica,utilidades,ropa,salud,otros',
        ]);

        $expense = Expense::find($id);

        if ($expense->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'success' => false,
            ], 401);
        }

        $expense->update($data);

        return response()->json([
            'message' => 'Expense updated successfully',
            'data' => $expense,
            'success' => true,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $expense = Expense::find($id);

        if ($expense->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
                'success' => false,
            ], 401);
        }

        $expense->delete();

        return response()->json([
            'message' => 'Expense deleted successfully',
            'success' => true,
        ]);
    }

    public function listCategory(Request $request, $category)
    {
        if (!in_array($category, ['comida', 'ocio', 'electronica', 'utilidades', 'ropa', 'salud', 'otros'])) {
            return response()->json([
                'message' => 'Invalid category',
                'success' => false,
            ], 400);
        }

        $expenses = $request->user()->expenses()->where('category', $category)->get();

        if ($expenses->isEmpty()) {
            return response()->json([
                'message' => 'No expenses found',
                'success' => false,
            ], 404);
        }

        return response()->json([
            'data' => $expenses,
            'success' => true,
        ]);
    }
}
