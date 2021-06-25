<?php
namespace App\Http\Controllers;

use App\Models\OfferSettings;
use Illuminate\Http\Request;
use Redirect;

class OfferSettingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $type                 = $request->get('type');
        $data['offerComment'] = OfferSettings::whereType($type)->first();
        $data['type']         = $type;
        return view('offersettings.index', $data);
    }

    /**
     * [update description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function update(Request $request)
    {
        $data = $request->all();
        OfferSettings::where('id', $data['id'])->update(['data' => $data['data']]);
        return Redirect::back()->with('success', __('main.update_standard_text_msg'));
    }

}
