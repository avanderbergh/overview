<?php

namespace App\Http\Controllers;

use JavaScript;
use Avanderbergh\Schoology\Facades\Schoology;

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
        return view('app')->with('realm_id', $realm_id);
    }
}
