<?php

namespace App\Http\Controllers;

use App\Models\UniIntegration;
use Illuminate\Http\Request;
use Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        @Session::put('order_search', ['search_by_department' => \Auth::user()->department_id]);
        return view('home');
    }

    //Getting the code from UNI to create the access token
    public function getCode(Request $request)
    {
        $data = $request->all();
        UniIntegration::createAccessTokenFromCode($data);
        return \Redirect::route('home')->with('success', "UNI credentials accepted and token generated successfully");
    }

    //getting the company detail
    public function getCompanyDetails()
    {
        UniIntegration::fetchCompanyDetails();
    }
}
