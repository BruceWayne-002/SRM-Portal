<!-- resources/views/exams/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Edit Exam Schedule')

@section('main')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Exam Schedule</h5>
        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <h6>Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label">Subject *</label>
                    <!-- Update the subject select options to include data-duration attribute -->
<select class="form-select @error('subject_id') is-invalid @enderror" 
        id="subject_id" name="subject_id" required>
    <option value="">Select Subject</option>
    @foreach($subjects as $subject)
    <option value="{{ $subject->id }}" 
            data-duration="{{ $subject->duration_hours }}"
            {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
        {{ $subject->code }} - {{ $subject->name }}
    </option>
    @endforeach
</select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_type" class="form-label">Exam Type *</label>
                    <select class="form-select @error('exam_type') is-invalid @enderror" 
                            id="exam_type" name="exam_type" required>
                        <option value="">Select Type</option>
                        <option value="midterm" {{ old('exam_type', $exam->exam_type) == 'midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final</option>
                        <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="assignment" {{ old('exam_type', $exam->exam_type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </select>
                    @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_date" class="form-label">Exam Date *</label>
                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror" 
                           id="exam_date" name="exam_date" 
                           value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" required>
                    @error('exam_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_time" class="form-label">Exam Time *</label>
                    <input type="time" class="form-control @error('exam_time') is-invalid @enderror" 
                           id="exam_time" name="exam_time" 
                           value="{{ old('exam_time', \Carbon\Carbon::parse($exam->exam_time)->format('H:i')) }}" required>
                    @error('exam_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- In BOTH create.blade.php AND edit.blade.php -->
<!-- Time Session Field -->
<div class="col-md-6 mb-3">
    <label for="time_session" class="form-label">Time Session *</label>
    <div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="time_session" 
                   id="fn" value="FN" 
                   {{ old('time_session', isset($exam) ? $exam->time_session : '') == 'FN' ? 'checked' : '' }}>
            <label class="form-check-label" for="fn">Forenoon (FN) - Before 12:00</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="time_session" 
                   id="an" value="AN" 
                   {{ old('time_session', isset($exam) ? $exam->time_session : '') == 'AN' ? 'checked' : '' }}>
            <label class="form-check-label" for="an">Afternoon (AN) - 12:00 & After</label>
        </div>
    </div>
    @error('time_session')
        <div class="text-danger" style="font-size: 0.875em;">{{ $message }}</div>
    @enderror
</div>
<!-- Replace the duration section with this: -->
<div class="col-md-6 mb-3">
    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
           id="duration_minutes" name="duration_minutes" 
           value="{{ old('duration_minutes', $exam->duration_minutes) }}">
    @error('duration_minutes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                <div class="col-md-12 mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" 
                              id="instructions" name="instructions" rows="3">{{ old('instructions', $exam->instructions) }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Exam</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Set minimum date to today
    document.getElementById('exam_date').min = new Date().toISOString().split('T')[0];
    
    // Function to update duration based on selected subject
    function updateDurationFromSubject() {
        const subjectSelect = document.getElementById('subject_id');
        const durationInput = document.getElementById('duration_minutes');
        
        // Get the selected subject option
        const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
        const subjectId = selectedOption.value;
        
        if (subjectId) {
            // You might need to fetch subject details via AJAX
            // or you can store duration in data attribute like in create form
            
            // If you have duration in data attribute like create form:
            const hours = selectedOption.getAttribute('data-duration');
            if (hours) {
                const minutes = hours * 60;
                durationInput.value = minutes;
            }
        }
    }
    
    // Update duration when subject changes
    document.getElementById('subject_id').addEventListener('change', updateDurationFromSubject);
    
    // Initialize duration on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateDurationFromSubject();
    });
</script>
<script>
    // Set minimum date to today
    document.getElementById('exam_date').min = new Date().toISOString().split('T')[0];
    
    // Function to auto-select FN/AN based on exam time
    function autoSelectTimeSession() {
        const examTimeInput = document.getElementById('exam_time');
        const fnRadio = document.getElementById('fn');
        const anRadio = document.getElementById('an');
        
        if (examTimeInput.value) {
            const timeValue = examTimeInput.value;
            const hour = parseInt(timeValue.split(':')[0]);
            
            // FN = Before 12:00 (00:00 - 11:59)
            // AN = 12:00 and after (12:00 - 23:59)
            if (hour < 12) {
                fnRadio.checked = true;
            } else {
                anRadio.checked = true;
            }
        }
    }
    
    // Auto-select when exam time changes
    document.getElementById('exam_time').addEventListener('change', autoSelectTimeSession);
    document.getElementById('exam_time').addEventListener('input', autoSelectTimeSession);
    
    // Also auto-select when page loads (for edit form)
    document.addEventListener('DOMContentLoaded', function() {
        autoSelectTimeSession();
    });
    
    // For Edit Form Only - Duration auto-fill from subject
    @if(isset($exam))
    const subjectSelect = document.getElementById('subject_id');
    const durationInput = document.getElementById('duration_minutes');
    const durationHidden = document.getElementById('duration_minutes_hidden');
    
    if (subjectSelect) {
        subjectSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const hours = selectedOption.getAttribute('data-duration');
            
            if (hours) {
                const minutes = hours * 60;
                durationInput.value = minutes;
                durationHidden.value = minutes;
            }
        });
        
        // Initialize duration on page load for edit form
        const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
        const hours = selectedOption.getAttribute('data-duration');
        if (hours) {
            const minutes = hours * 60;
            durationInput.value = minutes;
            durationHidden.value = minutes;
        }
    }
    @endif
</script>
@endpush
@endsection