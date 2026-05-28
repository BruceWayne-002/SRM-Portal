```php
@extends('layouts.app')

@section('title', 'Schedule New Exam')

@section('main')

<div class="card shadow-sm border-0">

    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">
            Schedule New Exam
        </h4>
    </div>

    <div class="card-body">

        <form action="{{ route('admin.exams.store') }}" method="POST">
            @csrf

            <div class="row">

                {{-- SUBJECT --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Subject *
                    </label>

                    <select
                        class="form-select @error('subject_id') is-invalid @enderror"
                        id="subject_id"
                        name="subject_id"
                        required
                    >

                        <option value="">
                            -- Select Subject --
                        </option>

                        @php

                            $uniqueSubjects = $subjects
                                ->unique(function ($item) {
                                    return strtolower($item->code . '-' . $item->name);
                                });

                        @endphp

                        @foreach($uniqueSubjects as $subject)

                            <option
                                value="{{ $subject->id }}"
                                data-duration="{{ $subject->duration_hours }}"
                                data-code="{{ $subject->code }}"
                                data-name="{{ $subject->name }}"
                            >

                                {{ $subject->code }}
                                -
                                {{ $subject->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('subject_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- SUBJECT PREVIEW --}}
                    <div
                        id="subjectPreview"
                        class="mt-3 p-3 bg-light rounded border d-none"
                    >

                        <small class="text-muted">
                            Selected Subject
                        </small>

                        <div id="selectedSubjectInfo"></div>

                    </div>

                </div>

                {{-- EXAM TYPE --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Exam Type *
                    </label>

                    <select
                        class="form-select @error('exam_type') is-invalid @enderror"
                        name="exam_type"
                        required
                    >

                        <option value="">
                            Select Type
                        </option>

                        <option value="midterm">
                            Midterm
                        </option>

                        <option value="final">
                            Final
                        </option>

                        <option value="quiz">
                            Quiz
                        </option>

                        <option value="assignment">
                            Assignment
                        </option>

                    </select>

                </div>

                {{-- EXAM DATE --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Exam Date *
                    </label>

                    <input
                        type="date"
                        class="form-control"
                        id="exam_date"
                        name="exam_date"
                        required
                    >

                </div>

                {{-- EXAM TIME --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Exam Time *
                    </label>

                    <input
                        type="time"
                        class="form-control"
                        id="exam_time"
                        name="exam_time"
                        required
                    >

                </div>

                {{-- TIME SESSION --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Time Session *
                    </label>

                    <div class="border rounded p-3">

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="time_session"
                                id="fn"
                                value="FN"
                            >

                            <label class="form-check-label" for="fn">
                                <strong>FN</strong>
                                (Forenoon)
                            </label>

                        </div>

                        <div class="form-check">

                            <input
                                class="form-check-input"
                                type="radio"
                                name="time_session"
                                id="an"
                                value="AN"
                            >

                            <label class="form-check-label" for="an">
                                <strong>AN</strong>
                                (Afternoon)
                            </label>

                        </div>

                    </div>

                </div>

                {{-- DURATION --}}
                <div class="col-md-6 mb-4">

                    <label class="form-label fw-semibold">
                        Duration *
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            class="form-control"
                            id="duration_minutes"
                            name="duration_minutes"
                            placeholder="Enter duration"
                            max="200"
                        >

                        <span class="input-group-text">
                            minutes
                        </span>

                    </div>

                    <small class="text-muted">
                        Maximum allowed duration is 200 minutes
                    </small>

                </div>

                {{-- CLASS SELECTION --}}
                <div class="col-md-12 mb-4">

                    <div class="card border shadow-sm">

                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>

                                    <h5 class="fw-bold mb-1">
                                        🎓 Select Courses / Classes
                                    </h5>

                                    <small class="text-muted">
                                        Choose which departments are writing this exam
                                    </small>

                                </div>

                                <span class="badge bg-primary fs-6">
                                    <span id="selectedCount">0</span>
                                    Selected
                                </span>

                            </div>

                            {{-- SEARCH --}}
                            <div class="mb-3">

                                <input
                                    type="text"
                                    id="classSearch"
                                    class="form-control"
                                    placeholder="Search class..."
                                >

                            </div>

                            @php

                                $classes = \App\Models\Student::select('class_name')
                                    ->whereNotNull('class_name')
                                    ->distinct()
                                    ->orderBy('class_name')
                                    ->pluck('class_name');

                            @endphp

                            <div
                                class="border rounded p-3 bg-light"
                                style="max-height: 300px; overflow-y:auto;"
                            >

                                <div class="row">

                                    @foreach($classes as $class)

                                        <div
                                            class="col-md-4 mb-3 class-item"
                                            data-name="{{ strtolower($class) }}"
                                        >

                                            <div class="border rounded bg-white p-3 h-100">

                                                <div class="form-check">

                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input class-checkbox"
                                                        name="classes[]"
                                                        value="{{ $class }}"
                                                        id="class_{{ $loop->index }}"
                                                    >

                                                    <label
                                                        class="form-check-label fw-semibold ms-2"
                                                        for="class_{{ $loop->index }}"
                                                    >

                                                        {{ $class }}

                                                    </label>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- INSTRUCTIONS --}}
                <div class="col-md-12 mb-4">

                    <label class="form-label fw-semibold">
                        Instructions
                    </label>

                    <textarea
                        class="form-control"
                        name="instructions"
                        rows="4"
                        placeholder="Enter exam instructions..."
                    ></textarea>

                </div>

            </div>

            {{-- BUTTONS --}}
            <div class="d-flex justify-content-between">

                <a
                    href="{{ route('admin.exams.index') }}"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Schedule Exam
                </button>

            </div>

        </form>

    </div>

</div>

@endsection


@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // MIN DATE
    // =========================

    const today = new Date().toISOString().split('T')[0];

    document.getElementById('exam_date').min = today;

    // =========================
    // SUBJECT PREVIEW
    // =========================

    const subjectSelect =
        document.getElementById('subject_id');

    const durationInput =
        document.getElementById('duration_minutes');

    const preview =
        document.getElementById('subjectPreview');

    const previewInfo =
        document.getElementById('selectedSubjectInfo');

    function updateSubjectPreview() {

        const option =
            subjectSelect.options[subjectSelect.selectedIndex];

        if(option.value){

            const code = option.dataset.code;

            const name = option.dataset.name;

            const hours = option.dataset.duration;

            const minutes = parseInt(hours) * 60;

            durationInput.value = minutes;

            previewInfo.innerHTML = `
                <strong>${code}</strong><br>
                ${name}<br>

                <span class="text-primary">
                    Duration: ${minutes} minutes
                </span>
            `;

            preview.classList.remove('d-none');

        } else {

            preview.classList.add('d-none');

            durationInput.value = '';

        }

    }

    subjectSelect.addEventListener(
        'change',
        updateSubjectPreview
    );

    // =========================
    // AUTO FN / AN
    // =========================

    const examTime =
        document.getElementById('exam_time');

    const fn =
        document.getElementById('fn');

    const an =
        document.getElementById('an');

    examTime.addEventListener('change', function(){

        if(!this.value) return;

        const hour =
            parseInt(this.value.split(':')[0]);

        if(hour < 12){

            fn.checked = true;

        } else {

            an.checked = true;

        }

    });

    // =========================
    // SEARCH CLASS
    // =========================

    const searchInput =
        document.getElementById('classSearch');

    const classItems =
        document.querySelectorAll('.class-item');

    searchInput.addEventListener('keyup', function(){

        const value = this.value.toLowerCase();

        classItems.forEach(item => {

            const name = item.dataset.name;

            item.style.display =
                name.includes(value)
                ? 'block'
                : 'none';

        });

    });

    // =========================
    // SELECTED COUNT
    // =========================

    const checkboxes =
        document.querySelectorAll('.class-checkbox');

    const selectedCount =
        document.getElementById('selectedCount');

    function updateCount(){

        let count = 0;

        checkboxes.forEach(box => {

            if(box.checked){

                count++;

            }

        });

        selectedCount.innerText = count;

    }

    checkboxes.forEach(box => {

        box.addEventListener('change', updateCount);

    });

});

</script>

@endpush
```
