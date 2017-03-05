<?php

namespace App\Http\Controllers;

use JavaScript;
use App\School;
use Carbon\Carbon;
use Avanderbergh\Schoology\Facades\Schoology;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class IndexController extends Controller
{
    public function index()
    {
        if (key_exists('realm_id', $_GET)) {
            $realm_id = $_GET["realm_id"];
            Schoology::authorize();
            JavaScript::put([
                'realm_id' => $realm_id,
                'user_id' => auth()->user()->id,
            ]);
        } else {
            return "No Realm ID";
        }
        Schoology::authorize();
        try {
            $school = School::findOrFail(session('schoology')['school_nid']);
        } catch (ModelNotFoundException $e){
            return "School not found, please ask your Schoology Administrator to configure your School";
        }
        if (Carbon::createFromDate($school->vaild_until) < Carbon::now()){
            return Carbon::createFromDate($school->valid_until).' '.Carbon::now();
            return "Your school subscription has expired, please ask your Schoology Administrator to renew the subscription.";
        } else {
            return view('app')->with('realm_id', $realm_id);
        }
    }
}
