<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MoisResource;
use App\Models\Mois;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MoisController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return MoisResource::collection(
            Mois::orderBy('ordre')->get(),
        );
    }
}
