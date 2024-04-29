<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Member\Models\Member;
use App\Modules\UserRole\Models\UserRole;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $id_user =  $user->id;

        $user_role = new UserRole();
        $user_role->id_user = $id_user;
        $user_role->id_role = 'b082f47e-16ee-4adb-a96e-da785e230cc3';
        $user_role->created_by = Auth::id();
        $user_role->save();

        $member = new Member();
        $member->id_user = $id_user;
        $member->nama = $request->name;
        $member->email = $request->email;
        $member->id_statusmembership = 'f273731c-9a0e-4452-b429-2592ecd75e87';
        $member->created_by = Auth::id();
        $member->save();

        // dd($user_role);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
