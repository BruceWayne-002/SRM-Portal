<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'Admin')->get();
        return view('superadmin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_code'     => 'required|string|max:20|unique:schools,code',
            'school_name'     => 'required|string|max:255',
            'school_address'  => 'required|string|max:255',
            'school_email'    => 'required|email|max:255',
            'school_contact'  => 'nullable|string|max:50',

            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|string|min:6|confirmed',
        ]);

        // Create the school
        $school = School::create([
            'code'           => $request->school_code,
            'name'           => $request->school_name,
            'address'        => $request->school_address,
            'email'          => $request->school_email,
            'contact_number' => $request->school_contact,
        ]);

        // Create the admin user linked to that school
        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'Admin',
            'school_code' => $school->code,
        ]);

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'School and Admin created successfully!');
    }

    public function show(User $admin)
    {
        $school = $admin->school_code ? School::where('code', $admin->school_code)->first() : null;
        return view('superadmin.admins.show', compact('admin', 'school'));
    }

    public function edit(User $admin)
    {
        $school = $admin->school_code ? School::where('code', $admin->school_code)->first() : null;
        return view('superadmin.admins.edit', compact('admin', 'school'));
    }

    public function update(Request $request, User $admin)
    {
        $request->validate([
            'school_code'     => 'required|string|max:20|unique:schools,code,' . $admin->school_code . ',code',
            'school_name'     => 'required|string|max:255',
            'school_address'  => 'required|string|max:255',
            'school_email'    => 'required|email|max:255',
            'school_contact'  => 'nullable|string|max:50',

            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $admin->id,
            'password'        => 'nullable|string|min:6|confirmed',
        ]);

        // Update school
        $school = School::updateOrCreate(
            ['code' => $request->school_code],
            [
                'name'           => $request->school_name,
                'address'        => $request->school_address,
                'email'          => $request->school_email,
                'contact_number' => $request->school_contact,
            ]
        );

        // Update admin
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->school_code = $school->code;

        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('superadmin.admins.index')
                         ->with('success', 'School and Admin updated successfully!');
    }

    public function destroy(User $admin)
    {
        if ($admin->role === 'Admin') {
            $admin->delete();
        }

        return redirect()->route('superadmin.admins.index')
                        ->with('success', 'Admin deleted successfully!');
    }

}
