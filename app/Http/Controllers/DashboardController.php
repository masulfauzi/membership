<?php

namespace App\Http\Controllers;

use App\Helpers\Permission;
use App\Modules\Member\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

use function Ramsey\Uuid\v1;

class DashboardController extends Controller
{
    public function index()
    {
        // dd(session()->all());
        // dd(Auth::user());

        if(session()->get('active_role')['id'] == 'b082f47e-16ee-4adb-a96e-da785e230cc3')
        {
            $data['member'] = Member::find(session()->get('id_member'));

            return view('member_dashboard', $data);
        }
        else{
            return view('dashboard');
        }


    }

    public function welcome(Request $request)
    {
        $data['member'] = Member::all();

        return view('welcome', $data);
    }

    public function changeRole($id_role)
    {
        $user = Auth::user();

        // get user's role
        $roles = Permission::getRole($user->id);
        if($roles->count() == 0) abort(403);
        $active_role = $roles->where('id', $id_role)->first()->only(['id', 'role']);
        // dd($active_role);
        // get user's menu
        $menus = Permission::getMenu($active_role);

        // get user's privilege
        $privileges = Permission::getPrivilege($active_role);
        $privileges = $privileges->mapWithKeys(function ($item, $key) {
                            return [$item['module'] => $item->only(['create', 'read', 'update', 'delete', 'show_menu'])];
                        });

        // store to session
        session(['menus' => $menus]);
        session(['roles' => $roles->pluck('role', 'id')->all()]);
        session(['privileges' => $privileges->all()]);
        session(['active_role' => $active_role]);

        return redirect()->route('dashboard')->with('message_success', 'Berhasil memperbarui role/session sebagai '.$active_role['role']);
    }

    public function forceLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('login');
    }
}
