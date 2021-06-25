<?php 
namespace App\Http\Controllers;
use Auth;
use Input;
use Redirect;
use Request;
use App\Helpers\MaskinstyringHelper;
use App\Http\Requests\accountingRequest;
use App\Models\Department;
use App\Models\accounting;
use Session;
use Response;

class AccountingController extends MaskinstyringController {

	protected $folder = 'accounting';
    protected $route = 'main.accounting.index';
	protected $title = 'main.accounting.title';
    protected $success = 'success';
    protected $error = 'error';
    protected $notfound = 'main.accounting.notfound';
    protected $createmsg = 'main.accounting.createsuccess';
    protected $updatemsg = 'main.accounting.updatesuccess';
    protected $deletemsg = 'main.accounting.deletesuccess';
    protected $accounting = 'main.accounting.title';
    protected $hourlogging = 'main.hourlogging.title';
    protected $error_msg_prefix = 'main.error_msg_prefix';
    protected $error_msg_suffix = 'main.error_msg_suffix'; 
    
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		if (!Request::get('page')) {
            Session::put('search', '');
        }
        if (Input::get('search')) {
            Session::put('search', Input::all());
        }
		$data['neworder']=array();
        $querystring=  $_GET;
        $data['sortorder']='ltkode';
        $data['sortby']='asc';
        if(!empty($querystring)){
	        $data=$this->get_querystring($querystring,'ltkode','asc');
	        $data['neworder']=$this->construct_orderby($data['orderby']);
        }
        $data['accounting'] = accounting::getaccounting(Session::get('search'),$data['sortorder'],$data['sortby']);
		return view('accounting.index', $data);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		$data['department'] = Department::lists('name','id');
		return view('accounting.form',$data);
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(accountingRequest $request)
	{
		$input = $request->all();
		$input['id'] = MaskinstyringHelper::gen_uuid();
		accounting::create($input);
		return Redirect::route($this->route)->with($this->success, trans($this->createmsg));
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show($id)
	{

	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		$data['accounting'] = accounting::findorFail($id);
		$data['department'] = Department::lists('name','id');
		return view('accounting.edit', $data);
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update(accountingRequest $request, $id)
	{
		$input = $request->all();
		$accounting = accounting::findorFail($id);
    	$accounting->fill($input);
		$accounting->save();
		return Redirect::route($this->route)->with($this->success, trans($this->updatemsg));
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		$accounting = accounting::findorFail($id);
		if(!$accounting){
			return Redirect::route($this->route)->with($this->error, trans($this->notfound));
		}	
		$accounting->delete();
		return Redirect::route($this->route)->with($this->success, trans($this->deletemsg));
	}
	
}
?>
