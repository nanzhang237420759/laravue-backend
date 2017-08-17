<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function isFollow(Request $request)
    {
        $user = Auth::user();
        $followers = $user->followers()->pluck('followed_id')->toArray();
        if (in_array($request->get('id'), $followers)) {
            return $this->responseSuccess('OK', ['followed' => true]);
        }
        return $this->responseSuccess('OK', ['followed' => false]);
    }

    public function followThisUser(Request $request)
    {
        $user = Auth::user();
        $userToFollow = User::find($request->get('id'));
        $followed = $user->followThisUser($userToFollow->id);

        if ( count($followed['attached']) > 0 ) {
            $user->increment('followings_count');
            $userToFollow->increment('followers_count');
            return $this->responseSuccess('OK', ['followed' => true]);
        }

        $user->decrement('followings_count');
        $userToFollow->decrement('followers_count');
        return $this->responseSuccess('OK', ['followed' => false]);
    }
}
