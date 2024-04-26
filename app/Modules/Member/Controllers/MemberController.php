<?php
namespace App\Modules\Member\Controllers;

use Form;
use App\Helpers\Logger;
use Illuminate\Http\Request;
use App\Modules\Log\Models\Log;
use App\Modules\Member\Models\Member;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
		
		$data['forms'] = array(
			'id_user' => ['User', Form::text("id_user", old("id_user"), ["class" => "form-control","placeholder" => ""]) ],
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
			'nama' => 'required',
			'email' => 'required',
			'foto' => 'required',
			
		]);

		$member = new Member();
		$member->id_user = $request->input("id_user");
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

		
		$data['forms'] = array(
			'id_user' => ['User', Form::text("id_user", $member->id_user, ["class" => "form-control","placeholder" => "", "id" => "id_user"]) ],
			'nama' => ['Nama', Form::text("nama", $member->nama, ["class" => "form-control","placeholder" => "", "id" => "nama"]) ],
			'email' => ['Email', Form::text("email", $member->email, ["class" => "form-control","placeholder" => "", "id" => "email"]) ],
			'foto' => ['Foto', Form::text("foto", $member->foto, ["class" => "form-control","placeholder" => "", "id" => "foto"]) ],
			
		);

		$text = 'membuka form edit '.$this->title;//.' '.$member->what;
		$this->log($request, $text, ['member.id' => $member->id]);
		return view('Member::member_update', array_merge($data, ['title' => $this->title]));
	}

	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'id_user' => 'required',
			'nama' => 'required',
			'email' => 'required',
			'foto' => 'required',
			
		]);
		
		$member = Member::find($id);
		$member->id_user = $request->input("id_user");
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
