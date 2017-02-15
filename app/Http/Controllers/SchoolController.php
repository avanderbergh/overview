<?php

namespace App\Http\Controllers;

use App\Admin;
use App\School;
use App\User;
use Avanderbergh\Schoology\Facades\Schoology;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function show(Request $request)
    {
        Schoology::Authorize();
        $sgy_user = Schoology::apiResult('users/me');
        $sgy_school = Schoology::apiResult('schools/'.$sgy_user->school_id);
        try {
            $school = School::findOrFail($sgy_school->id);
        } catch (ModelNotFoundException $e) {
//            Create the School
            $school = School::create([
                'id' => $sgy_school->id,
                'title' => $sgy_school->id,
                'address1' => $sgy_school->address1,
                'address2' => $sgy_school->address2,
                'city' => $sgy_school->city,
                'state' => $sgy_school->state,
                'postal_code' => $sgy_school->postal_code,
                'country' => $sgy_school->country,
                'website' => $sgy_school->website,
                'phone' => $sgy_school->phone,
                'user_quota' => 10,
                'valid_until' => Carbon::today()->addDays(14)
            ]);
            $school->save();
            $school = School::find($sgy_school->id);
        }
        try {
            $admin = Admin::findOrFail($sgy_user->id);
        } catch (ModelNotFoundException $e) {
            $admin = Admin::create([
                'id' => $sgy_user->id,
                'school_id' => $sgy_user->school_id,
                'name_first' => $sgy_user->name_first,
                'name_last' => $sgy_user->name_last,
                'name_display' => $sgy_user->name_display,
                'primary_email' => $sgy_user->primary_email,
                'picture_url' => $sgy_user->picture_url
            ]);
            $admin->save();
            $admin = Admin::find($sgy_user->id);
        }
        return view('config');
    }
}
