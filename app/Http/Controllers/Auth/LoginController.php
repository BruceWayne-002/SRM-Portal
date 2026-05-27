<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Student;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim((string) $request->email);
        $password   = trim((string) $request->password);

        $userTable = (new User)->getTable();

        $userQuery = User::query();

        $userQuery->where(function ($q) use ($loginInput, $userTable) {
            $q->whereRaw('LOWER(email) = ?', [strtolower($loginInput)]);

            if (Schema::hasColumn($userTable, 'employee_id')) {
                $q->orWhere('employee_id', $loginInput);
            }

            if (Schema::hasColumn($userTable, 'register_no')) {
                $q->orWhere('register_no', $loginInput);
            }

            if (Schema::hasColumn($userTable, 'student_id')) {
                $q->orWhere('student_id', $loginInput);
            }

            if (Schema::hasColumn($userTable, 'roll_no')) {
                $q->orWhere('roll_no', $loginInput);
            }
        });

        $user = $userQuery->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Invalid login ID or password',
            ])->withInput();
        }

        $valid = false;
        $role = strtolower(trim((string) ($user->role ?? '')));

        switch ($role) {

            case 'superadmin':
            case 'admin':
                if (!empty($user->password) && Hash::check($password, $user->password)) {
                    $valid = true;
                }
                break;

            case 'teacher':
                // 1. Normal password login
                if (!empty($user->password) && Hash::check($password, $user->password)) {
                    $valid = true;
                }
                // 2. First login using employee_id as password
                elseif (!empty($user->employee_id) && $password === trim((string) $user->employee_id)) {
                    $valid = true;

                    $user->update([
                        'password' => Hash::make($password),
                    ]);
                }
                // 3. First login using DOB as password
                elseif (!empty($user->dob)) {
                    try {
                        $dob = Carbon::parse($user->dob);

                        $dobFormats = [
                            $dob->format('dmY'),
                            $dob->format('d-m-Y'),
                            $dob->format('d/m/Y'),
                            $dob->format('Y-m-d'),
                            $dob->format('Ymd'),
                            $dob->format('m/d/Y'),
                            $dob->format('d.m.Y'),
                            $dob->format('Y/m/d'),
                            $dob->format('mdY'),
                        ];

                        if (in_array($password, $dobFormats, true)) {
                            $valid = true;

                            $user->update([
                                'password' => Hash::make($password),
                            ]);
                        } else {
                            $passwordNumbers = preg_replace('/[^0-9]/', '', $password);

                            foreach ($dobFormats as $dobFormat) {
                                $dobNumbers = preg_replace('/[^0-9]/', '', $dobFormat);

                                if ($passwordNumbers === $dobNumbers) {
                                    $valid = true;

                                    $user->update([
                                        'password' => Hash::make($password),
                                    ]);
                                    break;
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Teacher DOB parse failed: ' . $e->getMessage());
                    }
                }
                // 4. Optional default password
                elseif ($password === 'teacher123') {
                    $valid = true;

                    $user->update([
                        'password' => Hash::make($password),
                    ]);
                }
                break;

            case 'student':
                // Normal password login
                if (!empty($user->password) && Hash::check($password, $user->password)) {
                    $valid = true;
                } else {
                    $student = null;
                    $studentTable = (new Student)->getTable();

                    // Find student by user data
                    if (
                        !$student &&
                        !empty($user->register_no) &&
                        Schema::hasColumn($studentTable, 'register_no')
                    ) {
                        $student = Student::where('register_no', trim((string) $user->register_no))->first();
                    }

                    if (
                        !$student &&
                        !empty($user->register_no) &&
                        Schema::hasColumn($studentTable, 'roll_no')
                    ) {
                        $student = Student::where('roll_no', trim((string) $user->register_no))->first();
                    }

                    if (
                        !$student &&
                        !empty($user->student_id) &&
                        Schema::hasColumn($studentTable, 'student_id')
                    ) {
                        $student = Student::where('student_id', trim((string) $user->student_id))->first();
                    }

                    // Find student by entered login input
                    if (!$student && Schema::hasColumn($studentTable, 'register_no')) {
                        $student = Student::where('register_no', $loginInput)->first();
                    }

                    if (!$student && Schema::hasColumn($studentTable, 'roll_no')) {
                        $student = Student::where('roll_no', $loginInput)->first();
                    }

                    if (!$student && Schema::hasColumn($studentTable, 'student_id')) {
                        $student = Student::where('student_id', $loginInput)->first();
                    }

                    if (!$student && Schema::hasColumn($studentTable, 'email')) {
                        $student = Student::whereRaw('LOWER(email) = ?', [strtolower($loginInput)])->first();
                    }

                    if ($student && !empty($student->dob)) {
                        try {
                            $dob = Carbon::parse($student->dob);

                            $dobFormats = [
                                $dob->format('dmY'),
                                $dob->format('d-m-Y'),
                                $dob->format('d/m/Y'),
                                $dob->format('Y-m-d'),
                                $dob->format('Ymd'),
                                $dob->format('m/d/Y'),
                                $dob->format('d.m.Y'),
                                $dob->format('Y/m/d'),
                                $dob->format('mdY'),
                            ];

                            // DOB match
                            if (in_array($password, $dobFormats, true)) {
                                $valid = true;

                                $user->update([
                                    'password' => Hash::make($password),
                                ]);
                            } else {
                                $passwordNumbers = preg_replace('/[^0-9]/', '', $password);

                                foreach ($dobFormats as $dobFormat) {
                                    $dobNumbers = preg_replace('/[^0-9]/', '', $dobFormat);

                                    if ($passwordNumbers === $dobNumbers) {
                                        $valid = true;

                                        $user->update([
                                            'password' => Hash::make($password),
                                        ]);
                                        break;
                                    }
                                }
                            }

                            // register_no first-time login
                            if (
                                !$valid &&
                                !empty($user->register_no) &&
                                $password === trim((string) $user->register_no)
                            ) {
                                $valid = true;

                                $user->update([
                                    'password' => Hash::make($password),
                                ]);
                            }

                            // student_id first-time login
                            if (
                                !$valid &&
                                !empty($user->student_id) &&
                                $password === trim((string) $user->student_id)
                            ) {
                                $valid = true;

                                $user->update([
                                    'password' => Hash::make($password),
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::error('Student DOB parse failed: ' . $e->getMessage());
                        }
                    }
                }
                break;

            default:
                return back()->withErrors([
                    'email' => 'Role not found for this user.',
                ])->withInput();
        }

        if ($valid) {
            Auth::login($user);
            $request->session()->regenerate();

            switch ($role) {
                case 'superadmin':
                    return redirect('/superadmin');

                case 'admin':
                    return redirect('/admin/dashboard');

                case 'teacher':
                    return redirect('/teacher/dashboard');

                case 'student':
                    return redirect('/student/dashboard');

                default:
                    return redirect('/home');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid login ID or password',
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