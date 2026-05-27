@extends('layouts.app')

@section('page-title', isset($class) ? 'Edit Class - ' . $class->full_name : 'Create New Class')

@section('main')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0">
                <div class="card-header text-white text-center" style="background-color:#640d3c;">
                    <h4 class="mb-0">
                        @if(isset($class))
                            Edit: {{ $class->full_name }}
                        @else
                            Create New Class & Section
                        @endif
                    </h4>
                </div>
                
                <div class="card-body p-4">
                    <!-- Display validation errors at the top -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">Please fix the following errors:</h5>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ isset($class) ? route('admin.classes.update', $class) : route('admin.classes.store') }}" method="POST" id="classForm">
                        @csrf
                        @if(isset($class))
                            @method('PUT')
                        @endif
                        
                        <div class="row">
                            <!-- Class Details -->
                            <div class="col-md-6 mb-3">
                                <h5 class="text-primary mb-3">Class Details</h5>
                                
                                <!-- For Edit Mode - Show Current Class Info -->
                                @if(isset($class))
                                    <div class="mb-3">
    <label for="name" class="form-label">Class Name *</label>
    <input type="text"
           class="form-control @error('name') is-invalid @enderror"
           id="name"
           name="name"
           value="{{ old('name', $class->name ?? '') }}"
           required>

    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                                @else
                                    <!-- For Create Mode - Class Selection -->
                                    <div class="mb-3">
                                        <label for="class_selector" class="form-label">Select Existing Class or Create New *</label>
                                        <select class="form-control @error('class_selector') is-invalid @enderror" 
                                                id="class_selector" name="class_selector" required>
                                            <option value="">Select Existing Class</option>
                                            <option value="new" {{ old('class_selector') == 'new' ? 'selected' : '' }}>
                                                Create New Class
                                            </option>
                                            @foreach($existingClasses as $classItem)
                                                <option value="{{ $classItem->name }}" 
                                                        {{ old('class_selector') == $classItem->name ? 'selected' : '' }}>
                                                   {{ $classItem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_selector')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Select an existing class or choose "Create New Class"</small>
                                    </div>
                                    
                                    <!-- New Class Fields (hidden by default) -->
                                    <div id="new_class_fields" style="display: {{ old('class_selector') == 'new' ? 'block' : 'none' }};">                                 
                                        <div class="mb-3">
                                            <label for="new_name" class="form-label">New Class Name *</label>
                                            
                                            <!-- Course Type Selection -->
                                            <div class="mb-2">
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="course_type" id="course_arts" value="arts" 
                                                           {{ old('course_type') == 'arts' ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="course_arts">Arts</label>
                                                    
                                                    <input type="radio" class="btn-check" name="course_type" id="course_science" value="science" 
                                                           {{ old('course_type') == 'science' ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="course_science">Science</label>
                                                    
                                                    <input type="radio" class="btn-check" name="course_type" id="course_commerce" value="commerce" 
                                                           {{ old('course_type') == 'commerce' ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="course_commerce">Commerce</label>
                                                    
                                                    <input type="radio" class="btn-check" name="course_type" id="course_other" value="other" 
                                                           {{ old('course_type') == 'other' ? 'checked' : '' }} autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="course_other">Other</label>
                                                </div>
                                            </div>
                                            
                                            <!-- Arts Courses Dropdown -->
                                            <div id="arts_courses" class="course-dropdown mb-2" style="display: none;">
                                                <select class="form-control" id="arts_course_select" name="arts_course_select">
                                                    <option value="">Select Arts Course</option>
                                                    <option value="B.A. Tamil">B.A. Tamil</option>
                                                    <option value="B.A. English">B.A. English</option>
                                                    <option value="B.A. History">B.A. History</option>
                                                    <option value="B.A. Economics">B.A. Economics</option>
                                                    <option value="B.A. Sociology">B.A. Sociology</option>
                                                    <option value="B.A. Psychology">B.A. Psychology</option>
                                                    <option value="B.A. Philosophy">B.A. Philosophy</option>
                                                    <option value="B.A. Fine Arts">B.A. Fine Arts</option>
                                                    <option value="B.A. Geography">B.A. Geography</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Science Courses Dropdown -->
                                            <div id="science_courses" class="course-dropdown mb-2" style="display: none;">
                                                <select class="form-control" id="science_course_select" name="science_course_select">
                                                    <option value="">Select Science Course</option>
                                                    <option value="B.Sc. Mathematics">B.Sc. Mathematics</option>
                                                    <option value="B.Sc. Physics">B.Sc. Physics</option>
                                                    <option value="B.Sc. Chemistry">B.Sc. Chemistry</option>
                                                    <option value="B.Sc. Botany">B.Sc. Botany</option>
                                                    <option value="B.Sc. Zoology">B.Sc. Zoology</option>
                                                    <option value="B.Sc. Computer Science">B.Sc. Computer Science</option>
                                                    <option value="B.Sc. Information Technology">B.Sc. Information Technology</option>
                                                    <option value="B.Sc. Electronics">B.Sc. Electronics</option>
                                                    <option value="B.Sc. Biotechnology">B.Sc. Biotechnology</option>
                                                    <option value="B.Sc. Microbiology">B.Sc. Microbiology</option>
                                                    <option value="B.Sc. Biochemistry">B.Sc. Biochemistry</option>
                                                    <option value="B.Sc. Environmental Science">B.Sc. Environmental Science</option>
                                                    <option value="B.Sc. Geology">B.Sc. Geology</option>
                                                    <option value="B.Sc. Statistics">B.Sc. Statistics</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Commerce Courses Dropdown -->
                                            <div id="commerce_courses" class="course-dropdown mb-2" style="display: none;">
                                                <select class="form-control" id="commerce_course_select" name="commerce_course_select">
                                                    <option value="">Select Commerce Course</option>
                                                    <option value="B.Com">B.Com</option>
                                                    <option value="B.Com (Honours)">B.Com (Honours)</option>
                                                    <option value="B.Com Banking & Insurance">B.Com Banking & Insurance</option>
                                                    <option value="B.Com Computer Applications">B.Com Computer Applications</option>
                                                    <option value="B.Com Accounting & Finance">B.Com Accounting & Finance</option>
                                                    <option value="B.Com Marketing">B.Com Marketing</option>
                                                    <option value="BBA">BBA</option>
                                                    <option value="BBA Computer Applications">BBA Computer Applications</option>
                                                </select>
                                            </div>
                                            
                                            <!-- Other/Manual Input -->
                                            <div id="other_course" style="display: none;">
                                                <input type="text" class="form-control @error('new_name_manual') is-invalid @enderror" 
                                                       id="new_name_manual" name="new_name_manual" 
                                                       value="{{ old('new_name_manual') }}" 
                                                       placeholder="Enter class name manually">
                                            </div>
                                            
                                            <!-- Hidden field to store final class name -->
                                            <input type="hidden" id="new_name" name="new_name" value="{{ old('new_name') }}">
                                            
                                            @error('new_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            @error('new_name_manual')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Select a course from the dropdown or choose "Other" to enter manually</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Existing Class Info (hidden by default) -->
                                    <div id="existing_class_info" style="display: {{ old('class_selector') && old('class_selector') != 'new' ? 'block' : 'none' }};">
                                        <div class="alert alert-success">
                                            <strong>Selected Class:</strong> 
                                            <span id="selected_class_display"></span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3"
                                              placeholder="Optional description (e.g., Course details, specializations)">{{ old('description', $class->description ?? '') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Section Details -->
                            <div class="col-md-6 mb-3">
                                <h5 class="text-primary mb-3">Section Details</h5>
                                
                                <div class="mb-3">
                                    <label for="section_name" class="form-label">Section Name *</label>
                                    
                                    <select class="form-control @error('section_name') is-invalid @enderror" 
                                            id="section_name" name="section_name" required>
                                        <option value="">-- Select Section --</option>
                                        @foreach(range('A', 'J') as $letter)
                                            <option value="{{ $letter }}" 
                                                {{ old('section_name', $class->section_name ?? '') == $letter ? 'selected' : '' }}>
                                                {{ $letter }}
                                            </option>
                                        @endforeach
                                    </select>
                                    
                                    @error('section_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Existing Sections Display -->
                                    <div id="existing_sections" class="mt-2" style="display: none;">
                                        <small class="text-muted">Existing sections for this class:</small>
                                        <div id="sections_list" class="d-flex flex-wrap gap-1 mt-1"></div>
                                    </div>
                                </div>
                                
                                @if(isset($class))
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="is_active" name="is_active" 
                                               value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active Status
                                        </label>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('admin.classes.index') }}" 
                               class="btn btn-secondary me-md-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary-custom" id="submitBtn">
                                <i class="fas fa-save"></i> 
                                {{ isset($class) ? 'Update' : 'Create' }} Class & Section
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Store existing sections data
    let existingSectionsData = {};
    
    @if(!isset($class))
    // Class selector change event (only for create mode)
    $('#class_selector').change(function() {
        var selectedValue = $(this).val();
        
        if (selectedValue === 'new') {
            // Show new class fields
            $('#new_class_fields').show();
            $('#existing_class_info').hide();
            
            // Hide existing sections display
            $('#existing_sections').hide();
            $('#sections_list').empty();
            
            // Reset course selection
            $('input[name="course_type"]').prop('checked', false);
            $('.course-dropdown').hide();
            $('#other_course').hide();
            
            // Clear any existing section data
            existingSectionsData = {};
            
            // Enable submit button
            $('#submitBtn').prop('disabled', false);
        } 
        else if (selectedValue !== '') {
            // Show existing class info
            $('#new_class_fields').hide();
            $('#existing_class_info').show();
            
            // Get the selected option text
            var selectedOption = $(this).find('option:selected');
            var selectedText = selectedOption.text();
            
            $('#selected_class_display').text(selectedText);
            
            // Load existing sections for selected class
            loadExistingSections(selectedValue);
        } 
        else {
            // Nothing selected
            $('#new_class_fields').hide();
            $('#existing_class_info').hide();
            $('#existing_sections').hide();
            $('#sections_list').empty();
            
            $('#submitBtn').prop('disabled', false);
        }
    });
    
    // Trigger change on page load if there's a value
    if ($('#class_selector').val()) {
        $('#class_selector').trigger('change');
    }
    @else
    // For edit mode, load existing sections for this class
    loadExistingSections('{{ $class->name }}');
    @endif
    
    // Course type selection handling
    $('input[name="course_type"]').change(function() {
        var courseType = $(this).val();
        
        // Hide all course dropdowns
        $('.course-dropdown').hide();
        $('#other_course').hide();
        
        // Show selected course dropdown
        if (courseType === 'arts') {
            $('#arts_courses').show();
            // Clear other inputs
            $('#new_name_manual').val('');
            $('#new_name').val('');
        } else if (courseType === 'science') {
            $('#science_courses').show();
            // Clear other inputs
            $('#new_name_manual').val('');
            $('#new_name').val('');
        } else if (courseType === 'commerce') {
            $('#commerce_courses').show();
            // Clear other inputs
            $('#new_name_manual').val('');
            $('#new_name').val('');
        } else if (courseType === 'other') {
            $('#other_course').show();
            // Clear all selects
            $('#arts_course_select').val('');
            $('#science_course_select').val('');
            $('#commerce_course_select').val('');
        }
    });
    
    // Arts course selection
    $('#arts_course_select').change(function() {
        var selectedCourse = $(this).val();
        $('#new_name').val(selectedCourse);
    });
    
    // Science course selection
    $('#science_course_select').change(function() {
        var selectedCourse = $(this).val();
        $('#new_name').val(selectedCourse);
    });
    
    // Commerce course selection
    $('#commerce_course_select').change(function() {
        var selectedCourse = $(this).val();
        $('#new_name').val(selectedCourse);
    });
    
    // Manual input for other
    $('#new_name_manual').on('input', function() {
        $('#new_name').val($(this).val());
    });
    
    // Function to load existing sections for a class
    function loadExistingSections(className) {
        if (!className) return;
        
        $.ajax({
            url: "{{ route('admin.classes.sections') }}",
            method: 'GET',
            data: { name: className },
            success: function(response) {
                existingSectionsData[className] = response;
                
                if (response.length > 0) {
                    $('#sections_list').empty();
                    response.forEach(function(section) {
                        @if(isset($class))
                            if (section !== '{{ $class->section_name }}') {
                                $('#sections_list').append(
                                    '<span class="badge bg-secondary me-1">' + section + '</span>'
                                );
                            } else {
                                $('#sections_list').append(
                                    '<span class="badge bg-success me-1">' + section + ' (current)</span>'
                                );
                            }
                        @else
                            $('#sections_list').append(
                                '<span class="badge bg-secondary me-1">' + section + '</span>'
                            );
                        @endif
                    });
                    $('#existing_sections').show();
                    
                    // Update available sections in dropdown (filter out existing ones)
                    updateSectionDropdown(response, className);
                } else {
                    $('#existing_sections').hide();
                    $('#sections_list').empty();
                }
            },
            error: function() {
                $('#existing_sections').hide();
                $('#sections_list').empty();
            }
        });
    }
    
    // Function to update section dropdown based on existing sections
    function updateSectionDropdown(existingSections, className) {
        // Get current selected value
        var currentVal = $('#section_name').val();
        
        // Clear and repopulate section select
        $('#section_name').empty().append('<option value="">-- Select Section --</option>');
        
        // Add all sections from A to Z
        for (let i = 'A'.charCodeAt(0); i <= 'Z'.charCodeAt(0); i++) {
            const letter = String.fromCharCode(i);
            let optionText = letter;
            let disabled = false;
            
            // Check if section already exists for this class
            if (existingSections.includes(letter)) {
                @if(isset($class))
                    if (letter !== '{{ $class->section_name }}') {
                        optionText = letter + ' (already exists)';
                        disabled = true;
                    }
                @else
                    optionText = letter + ' (already exists)';
                    disabled = true;
                @endif
            }
            
            var option = $('<option>', {
                value: letter,
                text: optionText
            });
            
            if (disabled) {
                option.attr('disabled', true);
            }
            
            $('#section_name').append(option);
        }
        
        // Restore previous value if still available
        if (currentVal) {
            var option = $('#section_name option[value="' + currentVal + '"]');
            if (option.length && !option.is(':disabled')) {
                $('#section_name').val(currentVal);
            }
        }
        
        @if(isset($class))
        // Set current section as selected in edit mode
        $('#section_name').val('{{ $class->section_name }}');
        @endif
    }
    
    // Form submission validation
    $('#classForm').submit(function(e) {
        @if(!isset($class))
        // Validate that required fields are filled
        var classSelector = $('#class_selector').val();
        
        if (!classSelector) {
            e.preventDefault();
            alert('Please select whether to use an existing class or create a new one.');
            return false;
        }
        
        if (classSelector === 'new') {
            // Check course type selection
            var courseType = $('input[name="course_type"]:checked').val();
            if (!courseType) {
                e.preventDefault();
                alert('Please select a course type.');
                return false;
            }
            
            // Check course selection based on type
            if (courseType === 'arts') {
                if (!$('#arts_course_select').val()) {
                    e.preventDefault();
                    alert('Please select an Arts course.');
                    return false;
                }
            } else if (courseType === 'science') {
                if (!$('#science_course_select').val()) {
                    e.preventDefault();
                    alert('Please select a Science course.');
                    return false;
                }
            } else if (courseType === 'commerce') {
                if (!$('#commerce_course_select').val()) {
                    e.preventDefault();
                    alert('Please select a Commerce course.');
                    return false;
                }
            } else if (courseType === 'other') {
                if (!$('#new_name_manual').val().trim()) {
                    e.preventDefault();
                    alert('Please enter a class name.');
                    return false;
                }
            }
        }
        @endif
        
        // Validate section name
        var sectionName = $('#section_name').val();
        if (!sectionName) {
            e.preventDefault();
            alert('Please select a section name.');
            return false;
        }
        
        // Check if selected section is disabled (already exists)
        var selectedOption = $('#section_name option:selected');
        if (selectedOption.is(':disabled')) {
            e.preventDefault();
            alert('This section already exists for this class. Please select another section.');
            return false;
        }
        
        // Disable submit button to prevent double submission
        $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        return true;
    });
    
    // If there's an old value, pre-select the appropriate course
    @if(old('course_type'))
        var oldCourseType = '{{ old('course_type') }}';
        $('input[name="course_type"][value="' + oldCourseType + '"]').prop('checked', true).trigger('change');
        
        @if(old('course_type') == 'arts' && old('new_name'))
            setTimeout(function() {
                $('#arts_course_select').val('{{ old('new_name') }}');
            }, 100);
        @elseif(old('course_type') == 'science' && old('new_name'))
            setTimeout(function() {
                $('#science_course_select').val('{{ old('new_name') }}');
            }, 100);
        @elseif(old('course_type') == 'commerce' && old('new_name'))
            setTimeout(function() {
                $('#commerce_course_select').val('{{ old('new_name') }}');
            }, 100);
        @elseif(old('course_type') == 'other' && old('new_name_manual'))
            setTimeout(function() {
                $('#new_name_manual').val('{{ old('new_name_manual') }}');
            }, 100);
        @endif
    @endif
});
</script>
@endpush

@push('styles')
<style>
.btn-primary-custom {
    background: linear-gradient(135deg, #640d3c, #9D71C9);
    border: none;
    color: white;
    padding: 10px 25px;
    border-radius: 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(100, 13, 60, 0.3);
    color: white;
}

.btn-primary-custom:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Course selection buttons */
.btn-group .btn-check:checked + .btn-outline-primary {
    background: linear-gradient(135deg, #640d3c, #9D71C9);
    color: white;
    border-color: #640d3c;
}

.btn-group .btn-outline-primary {
    color: #640d3c;
    border-color: #640d3c;
}

.btn-group .btn-outline-primary:hover {
    background-color: #640d3c;
    color: white;
}

/* Course dropdowns */
.course-dropdown select.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px;
    font-size: 14px;
}

.course-dropdown select.form-control:focus {
    border-color: #640d3c;
    box-shadow: 0 0 0 0.2rem rgba(100, 13, 60, 0.25);
}

/* Manual input field */
#other_course input.form-control {
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 10px;
}

#other_course input.form-control:focus {
    border-color: #640d3c;
    box-shadow: 0 0 0 0.2rem rgba(100, 13, 60, 0.25);
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: white;
    font-size: 0.8em;
    padding: 0.25em 0.6em;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

/* Loading spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spinner {
    animation: spin 1s linear infinite;
}

/* Form styling */
.form-label {
    font-weight: 500;
    color: #333;
}

.alert {
    border-radius: 10px;
}
</style>
@endpush