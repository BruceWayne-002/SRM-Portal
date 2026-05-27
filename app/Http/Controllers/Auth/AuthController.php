<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Student;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = trim(strtolower($request->email));
        $password = trim($request->password);

        // Find user by email
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Invalid email or password'
            ])->withInput();
        }

        $valid = false;
        $role = strtolower(trim($user->role ?? ''));

        // SUPER ADMIN
        if ($role === 'superadmin') {
            if (!empty($user->password) && Hash::check($password, $user->password)) {
                $valid = true;
            }
        }

        // ADMIN
        elseif ($role === 'admin') {
            if (!empty($user->password) && Hash::check($password, $user->password)) {
                $valid = true;
            }
        }

        // TEACHER
        elseif ($role === 'teacher') {
            // Normal password login
            if (!empty($user->password) && Hash::check($password, $user->password)) {
                $valid = true;
            }
            // First login with employee_id
            elseif (!empty($user->employee_id) && $password === trim((string)$user->employee_id)) {
                $valid = true;

                $user->update([
                    'password' => Hash::make($password)
                ]);
            }
            // Optional default password
            elseif ($password === 'teacher123') {
                $valid = true;

                $user->update([
                    'password' => Hash::make($password)
                ]);
            }
        }

        // STUDENT
        elseif ($role === 'student') {
            // Normal password login
            if (!empty($user->password) && Hash::check($password, $user->password)) {
                $valid = true;
            } else {
                $student = null;

                // Find student by register_no
                if (!empty($user->register_no)) {
                    $student = Student::where('register_no', trim($user->register_no))->first();

                    if (!$student) {
                        $student = Student::where('roll_no', trim($user->register_no))->first();
                    }
                }

                // Fallback by email
                if (!$student) {
                    $student = Student::whereRaw('LOWER(email) = ?', [$email])->first();
                }

                if ($student && !empty($student->dob)) {
                    try {
                        $dob = Carbon::parse($student->dob);

                        $dobFormats = [
                            $dob->format('dmY'),    // 25042004
                            $dob->format('d-m-Y'),  // 25-04-2004
                            $dob->format('d/m/Y'),  // 25/04/2004
                            $dob->format('Y-m-d'),  // 2004-04-25
                            $dob->format('Ymd'),    // 20040425
                            $dob->format('m/d/Y'),  // 04/25/2004
                            $dob->format('d.m.Y'),  // 25.04.2004
                            $dob->format('Y/m/d'),  // 2004/04/25
                            $dob->format('mdY'),    // 04252004
                        ];

                        // Allow direct format match
                        if (in_array($password, $dobFormats, true)) {
                            $valid = true;

                            $user->update([
                                'password' => Hash::make($password)
                            ]);
                        } else {
                            // Allow numeric-only compare
                            $passwordNumbers = preg_replace('/[^0-9]/', '', $password);

                            foreach ($dobFormats as $dobFormat) {
                                $dobNumbers = preg_replace('/[^0-9]/', '', $dobFormat);

                                if ($passwordNumbers === $dobNumbers) {
                                    $valid = true;

                                    $user->update([
                                        'password' => Hash::make($password)
                                    ]);
                                    break;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Student DOB parse error: ' . $e->getMessage());
                    }
                }
            }
        }

        if ($valid) {
            Auth::login($user);
            $request->session()->regenerate();

            switch ($role) {
                case 'superadmin':
                    return redirect()->route('superadmin.schools.index');

                case 'admin':
                    return redirect()->route('admin.dashboard');

                case 'teacher':
                    return redirect()->route('teacher.dashboard');

                case 'student':
                    return redirect()->route('student.dashboard');

                default:
                    return redirect()->route('home');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid email or password'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}