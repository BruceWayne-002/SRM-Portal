<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Teacher, and Student can view subjects
        return in_array($user->role, ['Admin', 'Teacher', 'Student']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Subject $subject): bool
    {
        return match($user->role) {
            'Admin' => true,
            'Teacher' => $this->canTeacherView($user, $subject),
            'Student' => $this->canStudentView($user, $subject),
            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin can create subjects
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subject $subject): bool
    {
        // Only Admin can update subjects
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subject $subject): bool
    {
        // Only Admin can delete subjects
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subject $subject): bool
    {
        // Only Admin can restore deleted subjects
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subject $subject): bool
    {
        // Only Admin can permanently delete subjects
        return $user->role === 'Admin';
    }

    /**
     * Helper method for teacher viewing logic
     */
    private function canTeacherView(User $user, Subject $subject): bool
    {
        // Check if teacher teaches this subject
        // Example: return $subject->teachers->contains($user->id);
        return true; // Or implement your logic
    }

    /**
     * Helper method for student viewing logic
     */
    private function canStudentView(User $user, Subject $subject): bool
    {
        // Check if student is enrolled in this subject
        // Example: return $user->subjects->contains($subject->id);
        return true; // Or implement your logic
    }
}