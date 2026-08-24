<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OptionResource;
use App\Models\Option;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class OptionController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return OptionResource::collection(
            Option::orderBy('nom')->get(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100', 'unique:options,nom'],
        ]);

        return (new OptionResource(Option::create($data)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Option $option): OptionResource
    {
        return new OptionResource($option);
    }

    public function update(Request $request, Option $option): OptionResource
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('options')->ignore($option)],
        ]);

        $option->update($data);

        return new OptionResource($option);
    }

    public function destroy(Option $option): JsonResponse
    {
        $option->delete();

        return response()->json(['message' => 'Option supprimée.']);
    }
}
