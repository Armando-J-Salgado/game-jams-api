<?php

namespace App\Http\Controllers\Actions;

use App\Http\Controllers\Controller;
use App\Http\Resources\HandoverResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Handover;
use App\Http\Requests\SubmitHandoverRequest;

class SubmitHandoverController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(SubmitHandoverRequest $request, Handover $handover)
    {
        // Enforce policy via Gate (HandoverPolicy already verifies if user belongs to the associated team)
        Gate::authorize('update', $handover);

        $handover->update([
            'attachment' => $request->attachment,
            'is_delivered' => true,
            'date_of_submission' => now(),
        ]);

        return response()->json([
            'message' => 'Handover submitted successfully',
            'data' => new HandoverResource($handover->fresh()),
        ]);
    }
}
