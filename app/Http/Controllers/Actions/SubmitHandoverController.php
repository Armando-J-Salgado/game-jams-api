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
     * Submit Handover
     *
     * Submit an attachment for a pending handover.
     *
     * @group Handover Actions
     * @authenticated
     *
     * @urlParam handover int required The ID of the handover to submit. Example: 1
     * @bodyParam attachment string required URL to the delivery attachment. Example: https://github.com/my-repo
     *
     * @response 200 {
     *   "message": "Handover submitted successfully",
     *   "data": {"id": 1, "attachment": "https://github.com/my-repo", "is_delivered": true}
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 403 {
     *   "message": "This action is unauthorized."
     * }
     * @response 404 {
     *   "message": "Handover not found."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {"attachment": ["The attachment field is required."]}
     * }
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
