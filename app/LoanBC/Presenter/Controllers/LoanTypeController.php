<?php

declare(strict_types=1);

namespace App\LoanBC\Presenter\Controllers;

use App\LoanBC\Infrastructure\Persistence\Model\LoanTypeModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

final readonly class LoanTypeController
{
    public function index(): JsonResponse
    {
        $types = LoanTypeModel::all(['id', 'name']);

        return response()->json($types);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:loan_types,name',
        ]);

        $type = LoanTypeModel::create([
            'id' => Uuid::uuid7()->toString(),
            'name' => $data['name'],
        ]);

        return response()->json($type, 201);
    }
}
