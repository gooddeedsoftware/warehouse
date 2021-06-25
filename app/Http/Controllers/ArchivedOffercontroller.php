<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Offer;
use App\Models\User;
use App\Models\Department;
use App\Models\DropdownHelper;
use Input;
use Session;


class ArchivedOffercontroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        if (Input::get('search') || Input::get('search_status')|| Session::get('archived_offer_search') || Input::get('order_search_by_department') ) {
//            $input = Input::all();
//            $data['search_status'] = Input::get('search_status');
//            $data['search_str'] = Input::get('search');
//
//            if (Session::get('archived_offer_search')) {
//                $input = Session::get('archived_offer_search');
//                $data['search_status'] = isset($input['search_status']) ? $input['search_status'] : "";
//                $data['search_str'] = isset($input['search']) ? $input['search'] : "";
//                if (Input::get('search') == "") {
//                    $data['search_str'] = "";
//                    $input['search'] = "";
//                }
//                if (Input::get('search_status') == "" ) {
//                    $data['search_status'] = "";
//                    $input['search_status'] = "";
//                }
//                if (Input::get('order_search_by_department') == "" ) {
//                    $data['search_by_department'] = "";
//                    $input['search_by_department'] = "";
//                }
//            }
//
//            if (Input::get('search')) {
//                $input['search'] = Input::get('search');
//                $data['search_str'] = Input::get('search');
//            }
//             if (Input::get('search_status')) {
//                $input['search'] = Input::get('search') ? Input::get('search') : "";
//                $data['search_status'] = Input::get('search_status');
//                $input['search_status'] = Input::get('search_status');
//            }
//
//            if (Input::get('order_search_by_department')) {
//                $input['search'] = Input::get('search') ? Input::get('search') : "";
//                $data['search_by_department'] = Input::get('order_search_by_department');
//                $input['search_by_department'] = Input::get('order_search_by_department');
//            }
//            Session::put('archived_offer_search',$input);
//        }
        $data = @Session::get('archived_offer_search') ? @Session::get('archived_offer_search') : [];
        @Input::all() ? Session::put('archived_offer_search', array_merge($data, @Input::all())) : '' ;

        $data['neworder'] = array();
        $querystring = $_GET;
        $data['sortorder'] = 'offer_number';
        $data['sortby'] = 'desc';
        $data['offers'] = Offer::getOfferDetailsForIndex(Session::get('archived_offer_search'),$data['sortorder'],$data['sortby'], 2);
        $language = Session::get('language') ? Session::get('language') : 'no';
        $data['offer_status'] = Offer::getOfferStatus();
        $data['status'] = DropdownHelper::where('language', $language)->where('groupcode', '020')->whereIn('keycode', [4])->orderBy('keycode', 'asc')->lists('label', 'keycode');
        $data['status']['3'] = trans('main.offer.acceptorder');
        $data['status']['all'] = trans('main.offer.all_states');
        $data['users'] = User::getUsersDropDown();
        $data['departments'] = [1 => trans('main.offer.myoffers'), 2 => trans('main.offer.mydepartment'), 'all' => trans('main.offer.all_departments')];
        return view('offer.archived_offer', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
