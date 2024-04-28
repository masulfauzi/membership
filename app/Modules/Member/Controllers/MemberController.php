<?php
namespace App\Modules\Member\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Member\Models\Member;
use App\Modules\Users\Models\Users;
use App\Modules\StatusMembership\Models\StatusMembership;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class MemberController extends Controller
{
	use Logger;
	protected $log;
	protected $title = "Member";
	
	public function __construct(Log $log)
	{
		$this->log = $log;
	}

	public function index(Request $request)
	{
		$query = Member::query();
		if($request->has('search')){
			$search = $request->get('search');
			// $query->where('name', 'like', "%$search%");
		}
		$data['data'] = $query->paginate(10)->withQueryString();

		$this->log($request, 'melihat halaman manajemen data '.$this->title);
		return view('Member::member', array_merge($data, ['title' => $this->title]));
	}

	public function create(Request $request)
	{
		$ref_users = Users::all()->pluck('name','id');
		$ref_status_membership = StatusMembership::all()->pluck('status_membership','id');
		
		$data['forms'] = array(
			'id_user' => ['User', Form::select("id_user", $ref_users, null, ["class" => "form-control select2"]) ],
			'id_statusmembership' => ['Statusmembership', Form::select("id_statusmembership", $ref_status_membership, null, ["class" => "form-control select2"]) ],
			'no_anggota' => ['No Anggota', Form::text("no_anggota", old("no_anggota"), ["class" => "form-control","placeholder" => ""]) ],
			'nama' => ['Nama', Form::text("nama", old("nama"), ["class" => "form-control","placeholder" => ""]) ],
			'email' => ['Email', Form::text("email", old("email"), ["class" => "form-control","placeholder" => ""]) ],
			'foto' => ['Foto', Form::text("foto", old("foto"), ["class" => "form-control","placeholder" => ""]) ],
			
		);

		$this->log($request, 'membuka form tambah '.$this->title);
		return view('Member::member_create', array_merge($data, ['title' => $this->title]));
	}

	function store(Request $request)
	{
		$this->validate($request, [
			'id_user' => 'required',
			'id_statusmembership' => 'required',
			'no_anggota' => 'required',
			'nama' => 'required',
			'email' => 'required',
			'foto' => 'required',
			
		]);

		$member = new Member();
		$member->id_user = $request->input("id_user");
		$member->id_statusmembership = $request->input("id_statusmembership");
		$member->no_anggota = $request->input("no_anggota");
		$member->nama = $request->input("nama");
		$member->email = $request->input("email");
		$member->foto = $request->input("foto");
		
		$member->created_by = Auth::id();
		$member->save();

		$text = 'membuat '.$this->title; //' baru '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return redirect()->route('member.index')->with('message_success', 'Member berhasil ditambahkan!');
	}

	public function show(Request $request, Member $member)
	{
		$data['member'] = $member;

		$text = 'melihat detail '.$this->title;//.' '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return view('Member::member_detail', array_merge($data, ['title' => $this->title]));
	}

	public function edit(Request $request, Member $member)
	{
		$data['member'] = $member;

		$ref_users = Users::all()->pluck('name','id');
		$ref_status_membership = StatusMembership::all()->pluck('status_membership','id');
		
		$data['forms'] = array(
			'id_statusmembership' => ['Statusmembership', Form::select("id_statusmembership", $ref_status_membership, $member->id_statusmembership, ["class" => "form-control select2"]) ],
			'no_anggota' => ['No Anggota', Form::text("no_anggota", $member->no_anggota, ["class" => "form-control","placeholder" => "", "id" => "no_anggota"]) ],
			'nama' => ['Nama', Form::text("nama", $member->nama, ["class" => "form-control","placeholder" => "", "id" => "nama"]) ],
			'email' => ['Email', Form::text("email", $member->email, ["class" => "form-control","placeholder" => "", "id" => "email"]) ],
			'foto' => ['Foto', Form::text("foto", $member->foto, ["class" => "form-control","placeholder" => "", "id" => "foto"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return view('Member::member_update', array_merge($data, ['title' => $this->title]));
	}

	public function aktivasi(Request $request, String $id)
	{

		$no_anggota = rand(111111, 999999);

		$member = Member::find($id);
		$member->id_statusmembership = 'd966470c-2e9e-4ed9-a95c-be1503ff4dd1';
		$member->no_anggota = $no_anggota;
		$member->save();

		$text = 'aktivasi '.$this->title;//.' '.$member->what;
        $this->log($request, $text, ['member.id' => $member->id]);
        return redirect()->back()->with('message_success', 'Member berhasil diaktivasi!');
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_user' => 'required',
			'id_statusmembership' => 'required',
			'no_anggota' => 'required',
			'nama' => 'required',
			'email' => 'required',
			'foto' => 'required',
			
		]);
		
		$member = Member::find($id);
		$member->id_user = $request->input("id_user");
		$member->id_statusmembership = $request->input("id_statusmembership");
		$member->no_anggota = $request->input("no_anggota");
		$member->nama = $request->input("nama");
		$member->email = $request->input("email");
		$member->foto = $request->input("foto");
		
		$member->updated_by = Auth::id();
		$member->save();


		$text = 'mengedit '.$this->title;//.' '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return redirect()->route('member.index')->with('message_success', 'Member berhasil diubah!');
	}

	public function destroy(Request $request, $id)
	{
		$member = Member::find($id);
		$member->deleted_by = Auth::id();
		$member->save();
		$member->delete();

		$text = 'menghapus '.$this->title;//.' '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return back()->with('message_success', 'Member berhasil dihapus!');
	}

}
