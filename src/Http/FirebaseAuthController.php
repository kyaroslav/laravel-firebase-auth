<?php

namespace Kyaroslav\FirebaseAuth\Http;

use App\Http\Controllers\Auth\LoginController;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Kyaroslav\FirebaseAuth\FirebaseUser;
use Kyaroslav\FirebaseAuth\FirebaseSigninSource;
use TCG\Voyager\Models\Role as Role;

class FirebaseAuthController extends LoginController
{

    public function loginFirebase()
    {

        if(!empty(Auth::check()))
        {

            return redirect($this->redirectTo);

        }

        $data = Input::get();

        if(!empty($data)) {
            $signInId = (isset($data["sign_in_id"]) && !empty($data["sign_in_id"])) ? trim($data["sign_in_id"]) : null ;
            $check = $this->checkExisting($signInId);

            if (isset($check->user_id) && !empty($check->user_id)) {
                $id = $check->user_id;
                if (isset($data["name"]) && !empty($data["name"])) {
	                $name = (isset($data["name"])) ? urldecode($data["name"]) : uniqid("FBU-");
	                $user = User::find($id);  // Find the user using model and hold its reference
	                $user->name = $name;
	                $user->save();  // Update the data
                }
            } else {

                $source = $data["source"];
	            $pic = isset($data["pic"]) && !empty($data["pic"]) ? $data["pic"] : 'users/default.png';
	            $sourceId = FirebaseSigninSource::where('id', '=', $source)
                    ->where([
                    "active" => 1
                ])->first(['id']);

                if(!empty($sourceId->id))
                {
                    $sourceId = $sourceId->id;

                }
                else
                {

                    return ["status" => 0];

                }

	            $role = Role::where('name', 'user')->first();

                $id = User::insertGetId([
                    "name" => uniqid("FBU-"),
                    "email" => urldecode($data["email"]),
                    "password" => bcrypt($signInId),
                    "created_at" => Carbon::now()
                ]);

	            $user = User::find($id);
				if (!is_null($user) && !is_null($role)) {
		            $user->role()->associate($role);
		            $user->save();
				}

                FirebaseUser::insert([
                    "user_id" => $id,
                    "sign_in_id" => $signInId,
                    "source_id" => $sourceId,
                    "pic" => $pic,
                    "active" => 1,
                    "created_at" => Carbon::now()
                ]);

            }

            //printf(Auth::check());

            Auth::loginUsingId($id);

            return ["status" => 1];

        }

        $source = FirebaseSigninSource::where('active',1)->pluck('name')->toArray();

        $finalsrc = ["src" => $source];

        $data = [
            "redirectTo" => $this->redirectTo,
            "apiKey" => env('FIREBASE_AUTH_API_KEY'),
            "authDomain" => env('FIREBASE_AUTH_DOMAIN'),
            "db" => env('FIREBASE_DB_URL'),
            "source" => json_encode($finalsrc),
            "projectId" => env('FIREBASE_PROJET_ID'),
            "bucket" => env('FIREBASE_STORAGE_BUCKET'),
            "senderId" => env('FIREBASE_SENDER_ID')
        ];

        return view('fireview::auth_login')->with([
                "data" => $data
        ]);
    }

    private function checkExisting($signInId)
    {

        return  FirebaseUser::where([
            "sign_in_id" => $signInId,
            "active" => 1
        ])->first(['user_id']);

    }
}