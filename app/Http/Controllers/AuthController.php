<?php

namespace App\Http\Controllers;

use App\Http\Resources\Access\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Get authenticated user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->append('can', 'all_permissions');
        $user->load('avatar');
        $user->makeVisible('roles');

        return (new UserResource($user))->response();
    }

    /**
     * Update avatar.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->validate($request, [
            'avatar' => 'required|mimes:jpeg,png',
        ]);
        $user->handleFiles($request->allFiles(), 'avatar');

        return response()->json(null, 204);
    }

    /**
     * Delete avatar.
     */
    public function deleteAvatar(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->deleteDirectory('avatar');

        return response()->json(null, 204);
    }
}
