<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Admin can view any exams
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exam $exam): bool
    {
        // Admin can view any exam
        // Teacher can view exams they're assigned to (you can add this logic)
        // Students can view exams they're registered for
        return match($user->role) {
            'Admin' => true,
            'Teacher' => $this->canTeacherView($user, $exam),
            'Student' => $this->canStudentView($user, $exam),
            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin can create exams
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exam $exam): bool
    {
        // Only Admin can update exams
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Only Admin can delete exams
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exam $exam): bool
    {
        // Only Admin can restore deleted exams
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exam $exam): bool
    {
        // Only Admin can permanently delete exams
        return $user->role === 'Admin';
    }

    /**
     * Helper method for teacher viewing logic
     */
    private function canTeacherView(User $user, Exam $exam): bool
    {
        // Check if teacher is invigilating this exam
        // You'll need to implement your own logic based on your database structure
        // Example: return $exam->invigilators->contains($user->id);
        return false; // Default to false, implement your logic
    }

    /**
     * Helper method for student viewing logic
     */
    private function canStudentView(User $user, Exam $exam): bool
    {
        // Check if student is registered for this exam
        // You'll need to implement your own logic based on your database structure
        // Example: return $exam->students->contains($user->id);
        return false; // Default to false, implement your logic
    }
}