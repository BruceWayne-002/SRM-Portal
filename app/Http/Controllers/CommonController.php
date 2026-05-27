<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\School;

class CommonController extends Controller
{
    // Public page (about)
    public function index()
    {
        return view('about');
    }

    // Help pages based on role
    public function helpIndex()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->logoutWithError('Session expired. Please log in again.');
        }

        if (!$this->validateSchool($user)) {
            return $this->logoutWithError('Unauthorized school access. Please log in again.');
        }

        $schoolCode = $user->school_code;

        if ($user->role === 'Teacher') {
            return view('help.teacher', compact('schoolCode'));
        } elseif ($user->role === 'Student') {
            return view('help.student', compact('schoolCode'));
        } elseif ($user->role === 'Admin') {
            return view('help.admin', compact('schoolCode'));
        }

        return $this->logoutWithError('Unauthorized access. Please log in again.');
    }

    // Privacy pages based on role
    public function privacy()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->logoutWithError('Session expired. Please log in again.');
        }

        if (!$this->validateSchool($user)) {
            return $this->logoutWithError('Unauthorized school access. Please log in again.');
        }

        $schoolCode = $user->school_code;

        if ($user->role === 'Student') {
            return view('privacy.student', compact('schoolCode'));
        } elseif ($user->role === 'Teacher') {
            return view('privacy.teacher', compact('schoolCode'));
        } elseif ($user->role === 'Admin') {
            return view('privacy.admin', compact('schoolCode'));
        }

        return $this->logoutWithError('Unauthorized access. Please log in again.');
    }

    // Validate that user's school_code exists in the schools table
    private function validateSchool($user)
    {
        if (empty($user->school_code)) return false;

        return School::where('code', $user->school_code)->exists();
    }

    // Logout helper with error message
    private function logoutWithError($message)
    {
        Auth::logout();
        return redirect()->route('login')->withErrors(['error' => $message]);
    }
}
