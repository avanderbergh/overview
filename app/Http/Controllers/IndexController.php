<?php

namespace App\Http\Controllers;

use JavaScript;
use App\School;
use Carbon\Carbon;
use Illuminate\Http\Response;
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
        if (!$school->api_key || !$school->api_secret){
            return "The app has not been configured yet, please ask your Schoology administrator to add an API Key & Secret in the configuration screen";
        }
        if (Carbon::parse($school->valid_until) < Carbon::today()){
            return "Your school subscription has expired, please ask your Schoology Administrator to renew the subscription.";
        } else {
            return view('app')->with('realm_id', $realm_id);
        }
    }

    public function cookie_preload()
    {
        $html = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <title>Loading App...</title>
                </head>
                <body>
                    <p>Loading application...</p>
                    <script type="text/javascript">
                        self.close();
                    </script>
                </body>
            </html>';
        $response = new Response($html);
        return $response->withCookie(cookie('name', 'value', 5));
    }
}