<?php
namespace App\Modules\StatusMembership\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\StatusMembership\Models\StatusMembership;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StatusMembershipController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Status Membership";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = StatusMembership::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('StatusMembership::statusmembership', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		
		$data['forms'] = array(
			'status_membership' => ['Status Membership', Form::text("status_membership", old("status_membership"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('StatusMembership::statusmembership_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'status_membership' => 'required',
			
		]);

		$statusmembership = new StatusMembership();
		$statusmembership->status_membership = $request->input("status_membership");
		
		$statusmembership->created_by = Auth::id();
		$statusmembership->save();

		$text = 'membuat '.$this->title; //' baru '.$statusmembership->what;
		$this->log($request, $text, ['statusmembership.id' => $statusmembership->id]);
		return redirect()->route('statusmembership.index')->with('message_success', 'Status Membership berhasil ditambahkan!');
	}

	public function show(Request $request, StatusMembership $statusmembership)
	{
		$data['statusmembership'] = $statusmembership;

		$text = 'melihat detail '.$this->title;//.' '.$statusmembership->what;
		$this->log($request, $text, ['statusmembership.id' => $statusmembership->id]);
		return view('StatusMembership::statusmembership_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, StatusMembership $statusmembership)
	{
		$data['statusmembership'] = $statusmembership;

		
		$data['forms'] = array(
			'status_membership' => ['Status Membership', Form::text("status_membership", $statusmembership->status_membership, ["class" => "form-control","placeholder" => "", "id" => "status_membership"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$statusmembership->what;
		$this->log($request, $text, ['statusmembership.id' => $statusmembership->id]);
		return view('StatusMembership::statusmembership_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'status_membership' => 'required',
			
		]);
		
		$statusmembership = StatusMembership::find($id);
		$statusmembership->status_membership = $request->input("status_membership");
		
		$statusmembership->updated_by = Auth::id();
		$statusmembership->save();


		$text = 'mengedit '.$this->title;//.' '.$statusmembership->what;
		$this->log($request, $text, ['statusmembership.id' => $statusmembership->id]);
		return redirect()->route('statusmembership.index')->with('message_success', 'Status Membership berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$statusmembership = StatusMembership::find($id);
		$statusmembership->deleted_by = Auth::id();
		$statusmembership->save();
		$statusmembership->delete();

		$text = 'menghapus '.$this->title;//.' '.$statusmembership->what;
		$this->log($request, $text, ['statusmembership.id' => $statusmembership->id]);
		return back()->with('message_success', 'Status Membership berhasil dihapus!');
	}

}
