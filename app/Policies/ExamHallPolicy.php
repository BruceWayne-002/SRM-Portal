<?php

namespace App\Policies;

use App\Models\ExamHall;
use App\Models\User;

class ExamHallPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can view all halls
        // Teacher can view halls they're assigned to
        return in_array($user->role, ['Admin', 'Teacher']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExamHall $examHall): bool
    {
        return match($user->role) {
            'Admin' => true,
            'Teacher' => $this->canTeacherView($user, $examHall),
            default => false
        };
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin can create exam halls
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExamHall $examHall): bool
    {
        // Only Admin can update exam halls
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExamHall $examHall): bool
    {
        // Only Admin can delete exam halls
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExamHall $examHall): bool
    {
        // Only Admin can restore deleted exam halls
        return $user->role === 'Admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExamHall $examHall): bool
    {
        // Only Admin can permanently delete exam halls
        return $user->role === 'Admin';
    }

    /**
     * Helper method for teacher viewing logic
     */
    private function canTeacherView(User $user, ExamHall $examHall): bool
    {
        // Check if teacher is invigilating in this hall
        // Example: return $examHall->invigilators->contains($user->id);
        return false; // Implement your logic
    }
}