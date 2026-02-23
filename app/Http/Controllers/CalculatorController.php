<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function index(Request $request)
    {
        $calculations = Calculation::query()
            ->when(
                $request->user(),
                fn ($q) => $q->where('user_id', $request->user()->id)
            )
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($calculations);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'expression' => 'required|string|max:1000',
        ]);

        $expression = $request->input('expression');

        try {
            $result = $this->evaluateExpression($expression);

            $calculation = Calculation::create([
                'expression' => $expression,
                'result' => (string) $result,
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'id' => $calculation->id,
                'expression' => $calculation->expression,
                'result' => $calculation->result,
                'created_at' => $calculation->created_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid expression: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $calculation = Calculation::findOrFail($id);
        $calculation->delete();

        return response()->json(['message' => 'Calculation deleted']);
    }

    public function clear(): JsonResponse
    {
        Calculation::truncate();

        return response()->json(['message' => 'All calculations cleared']);
    }

    private function evaluateExpression(string $expression): float|int
    {
        $sanitized = $this->sanitizeExpression($expression);

        $this->validateExpression($sanitized);

        $phpExpression = $this->convertToPhp($sanitized);

        $result = eval('return ' . $phpExpression . ';');

        if (!is_numeric($result)) {
            throw new \InvalidArgumentException('Expression did not evaluate to a number');
        }

        return $result;
    }

    private function sanitizeExpression(string $expression): string
    {
        return preg_replace('/\s+/', '', $expression);
    }

    private function validateExpression(string $expression): void
    {
        $temp = str_replace('sqrt', '', $expression);

        if (!preg_match('/^[0-9+\-*\/^().]+$/', $temp)) {
            throw new \InvalidArgumentException('Invalid characters in expression');
        }

        $open = substr_count($expression, '(');
        $close = substr_count($expression, ')');

        if ($open !== $close) {
            throw new \InvalidArgumentException("Unbalanced parentheses: {$open} opening, {$close} closing");
        }
    }

    private function convertToPhp(string $expression): string
    {
        return str_replace('^', '**', $expression);
    }
}
