<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Access\UserResource;

class AuthController extends Controller
{
    /**
     * Get authenticated user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->append('can', 'all_permissions');
        $user->load('avatar');
        $user->makeVisible('roles');
        $data = (new UserResource($user))->toArray($request);
        return response()->json($data);
    }

    /**
     * Update avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'avatar' => 'required|mimes:jpeg,png',
        ]);
        $user->storeAvatar($request->avatar);
        return response()->json(null, 204);
    }

    /**
     * Delete avatar.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        $user->deleteAvatar();
        return response()->json(null, 204);
    }
}
