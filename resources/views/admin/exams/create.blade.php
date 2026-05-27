@extends('layouts.app')

@section('title', 'Schedule New Exam')

@section('main')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Schedule New Exam</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label">Subject *</label>
                    <select class="form-select @error('subject_id') is-invalid @enderror"
                            id="subject_id" name="subject_id" required>
                        <option value="">-- Select Subject --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" 
                                    data-duration="{{ $subject->duration_hours }}"
                                    data-code="{{ $subject->code }}"
                                    data-name="{{ $subject->name }}"
                                    data-class="{{ $subject->class_name }}"
                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->class_name }} | {{ $subject->code }} - {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    <!-- Selected Subject Preview -->
                    <div id="subjectPreview" class="mt-2 p-2 bg-light rounded d-none">
                        <small class="text-muted">Selected Subject:</small><br>
                        <span id="selectedSubjectInfo"></span>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_type" class="form-label">Exam Type *</label>
                    <select class="form-select @error('exam_type') is-invalid @enderror" 
                            id="exam_type" name="exam_type" required>
                        <option value="">Select Type</option>
                        <option value="midterm" {{ old('exam_type') == 'midterm' ? 'selected' : '' }}>Midterm</option>
                        <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final</option>
                        <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </select>
                    @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_date" class="form-label">Exam Date *</label>
                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror" 
                           id="exam_date" name="exam_date" value="{{ old('exam_date') }}" required>
                    @error('exam_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="exam_time" class="form-label">Exam Time *</label>
                    <input type="time" class="form-control @error('exam_time') is-invalid @enderror" 
                           id="exam_time" name="exam_time" value="{{ old('exam_time') }}" required>
                    @error('exam_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="time_session" class="form-label">Time Session *</label>
                    <div class="border p-3 rounded">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="time_session" 
                                   id="fn" value="FN" 
                                   {{ old('time_session') == 'FN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="fn">
                                <strong>FN</strong> (Forenoon - Before 12:00 PM)
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="time_session" 
                                   id="an" value="AN" 
                                   {{ old('time_session') == 'AN' ? 'checked' : '' }}>
                            <label class="form-check-label" for="an">
                                <strong>AN</strong> (Afternoon - 12:00 PM & After)
                            </label>
                        </div>
                    </div>
                    @error('time_session')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
    <label for="duration_minutes" class="form-label">Duration *</label>
    <div class="input-group">
        <input type="number" 
               class="form-control @error('duration_minutes') is-invalid @enderror" 
               id="duration_minutes" 
               name="duration_minutes" 
               value="{{ old('duration_minutes') }}"
               min="1"
               max="200"
               step="1"
               placeholder="Enter duration (Max 200 mins)">
        <span class="input-group-text">minutes</span>
    </div>

    @error('duration_minutes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <small class="text-muted">
        <i class="fas fa-info-circle"></i> Maximum allowed duration is 200 minutes
    </small>
</div>

                <div class="col-md-12 mb-3">
                    <label for="instructions" class="form-label">Instructions</label>
                    <textarea class="form-control @error('instructions') is-invalid @enderror" 
                              id="instructions" name="instructions" rows="4" 
                              placeholder="Enter exam instructions, rules, or special notes...">{{ old('instructions') }}</textarea>
                    @error('instructions')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-calendar-plus"></i> Schedule Exam
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    const examDateInput = document.getElementById('exam_date');
    if (examDateInput) {
        examDateInput.min = today;
    }
    
    // Subject selection handling
    const subjectSelect = document.getElementById('subject_id');
    const durationInput = document.getElementById('duration_minutes');
    const subjectPreview = document.getElementById('subjectPreview');
    const selectedSubjectInfo = document.getElementById('selectedSubjectInfo');
    
    function updateSubjectDetails() {
        const selectedOption = subjectSelect.options[subjectSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Update duration
            const hours = selectedOption.dataset.duration;
            if (hours) {
                const minutes = parseInt(hours) * 60;
                durationInput.value = minutes;
            } else {
                durationInput.value = '';
            }
            
            // Update subject preview
            const code = selectedOption.dataset.code || '';
            const name = selectedOption.dataset.name || '';
            const className = selectedOption.dataset.class || '';
            
            selectedSubjectInfo.innerHTML = `
                <strong>${className}</strong> | ${code} - ${name}<br>
                <span class="text-primary">Duration: ${hours} hour(s) (${parseInt(hours) * 60} minutes)</span>
            `;
            subjectPreview.classList.remove('d-none');
        } else {
            durationInput.value = '';
            subjectPreview.classList.add('d-none');
        }
    }
    
    subjectSelect.addEventListener('change', updateSubjectDetails);
    
    // Auto-select time session based on exam time
    const examTimeInput = document.getElementById('exam_time');
    const fnRadio = document.getElementById('fn');
    const anRadio = document.getElementById('an');
    
    function updateTimeSession() {
        if (examTimeInput.value) {
            const hour = parseInt(examTimeInput.value.split(':')[0]);
            if (hour < 12) {
                fnRadio.checked = true;
            } else {
                anRadio.checked = true;
            }
        }
    }
    
    examTimeInput.addEventListener('change', updateTimeSession);
    examTimeInput.addEventListener('input', updateTimeSession);
    
    // Initialize if subject is pre-selected
    if (subjectSelect.value) {
        updateSubjectDetails();
    }
});
</script>
@endpush