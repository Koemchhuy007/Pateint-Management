<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function districts(Request $request): JsonResponse
    {
        $districts = District::where('province_id', $request->province_id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($districts);
    }

    public function communities(Request $request): JsonResponse
    {
        $communities = Community::where('district_id', $request->district_id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($communities);
    }

    public function villages(Request $request): JsonResponse
    {
        $villages = Village::where('community_id', $request->community_id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($villages);
    }
}
