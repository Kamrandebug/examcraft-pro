@extends('admin.layouts.master')

@section('title', 'Bulk Import Questions')
@section('page_title', 'Bulk Import Questions')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.questions.index') }}">Question Bank</a></li>
    <li class="breadcrumb-item active">Bulk Import</li>
@endsection

@section('styles')
    <style>
        .upload-zone {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .upload-zone:hover {
            border-color: #c9a84c;
            background: #fafaf8;
        }

        .upload-zone.dragover {
            border-color: #c9a84c;
            background: #fffdf7;
        }

        .upload-icon {
            font-size: 48px;
            color: #c9a84c;
            margin-bottom: 12px;
        }

        .error-list {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 16px;
            margin-top: 16px;
        }

        .error-item {
            padding: 8px 0;
            border-bottom: 1px solid #f5c6cb;
            font-size: 13px;
        }

        .error-item:last-child {
            border-bottom: none;
        }

        .error-row {
            font-weight: 600;
            color: #721c24;
        }

        .error-msg {
            color: #721c24;
            margin-top: 4px;
        }

        .template-section {
            background: #f7f2e4;
            border: 1px solid #ddd8cc;
            border-radius: 6px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .template-section h6 {
            color: #1b2a4a;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .template-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            background: white;
            border: 1px solid #ddd8cc;
        }

        .template-table th,
        .template-table td {
            padding: 10px;
            border: 1px solid #ddd8cc;
            text-align: left;
        }

        .template-table th {
            background: #1b2a4a;
            color: white;
            font-weight: 600;
        }

        .template-table td {
            background: white;
            color: #333;
        }

        .btn-download {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 16px;
            background: #c9a84c;
            color: #1b2a4a;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-download:hover {
            background: #b8943e;
        }
    </style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Import Questions from File</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Import Result (if returned from failed validation) --}}
                @if(session('import_result'))
                    @php $result = session('import_result'); @endphp
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h4 class="alert-heading">
                            <i class="fas fa-exclamation-circle"></i> Import Failed
                        </h4>
                        <p>{{ $result['message'] }}</p>
                        <strong>Total rows:</strong> {{ $result['total'] }} |
                        <strong>Failed:</strong> {{ $result['failed'] }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    @if(!empty($result['errors']))
                    <div class="error-list">
                        <h6 style="margin-top: 0; color: #721c24;">
                            <i class="fas fa-times-circle"></i> Errors Found:
                        </h6>
                        @foreach($result['errors'] as $error)
                        <div class="error-item">
                            <div class="error-row">Row {{ $error['line'] }}:</div>
                            <div class="error-msg">{{ $error['error'] }}</div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                @endif

                {{-- Template Guide --}}
                <div class="template-section">
                    <h6>📋 File Format Guide</h6>
                    <p>Your CSV or Excel file must have these columns in this order:</p>
                    <table class="template-table">
                        <thead>
                            <tr>
                                <th>Question Text</th>
                                <th>Option A</th>
                                <th>Option B</th>
                                <th>Option C</th>
                                <th>Option D</th>
                                <th>Option E (optional)</th>
                                <th>Correct Answer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>What is 2+2?</td>
                                <td>3</td>
                                <td>4</td>
                                <td>5</td>
                                <td>6</td>
                                <td></td>
                                <td><strong>B</strong></td>
                            </tr>
                            <tr>
                                <td>Capital of France?</td>
                                <td>London</td>
                                <td>Paris</td>
                                <td>Berlin</td>
                                <td>Madrid</td>
                                <td></td>
                                <td><strong>B</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="margin-top: 12px; font-size: 12px; color: #666;">
                        <i class="fas fa-info-circle"></i>
                        <strong>Correct Answer:</strong> Use letter (A, B, C, D, etc.) matching the option column.
                        You can have 4-10 options. If you have more than 4, continue with Option E, F, G, H, I, J columns.
                    </p>
                </div>

                {{-- Import Form --}}
                <form action="{{ route('admin.questions.bulk-import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="form-group">
                        <label for="grade"><strong>Grade <span class="text-danger">*</span></strong></label>
                        <select name="grade" id="grade" class="form-control @error('grade') is-invalid @enderror" required>
                            <option value="">— Select Grade —</option>
                            <option value="O Level" {{ old('grade') == 'O Level' ? 'selected' : '' }}>O Level</option>
                            <option value="A Level" {{ old('grade') == 'A Level' ? 'selected' : '' }}>A Level</option>
                            <option value="10th Grade" {{ old('grade') == '10th Grade' ? 'selected' : '' }}>10th Grade</option>
                            <option value="9th Grade" {{ old('grade') == '9th Grade' ? 'selected' : '' }}>9th Grade</option>
                            <option value="8th Grade" {{ old('grade') == '8th Grade' ? 'selected' : '' }}>8th Grade</option>
                        </select>
                        @error('grade')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="subject"><strong>Subject <span class="text-danger">*</span></strong></label>
                        <select name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" required disabled>
                            <option value="">— Select Grade First —</option>
                        </select>
                        @error('subject')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="file"><strong>Upload File <span class="text-danger">*</span></strong></label>
                        <div class="upload-zone" id="uploadZone">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p><strong>Drop your CSV or Excel file here</strong></p>
                            <p style="color: #999; font-size: 13px; margin: 0;">or click to browse (Max 5MB)</p>
                            <input type="file" name="file" id="file" accept=".csv,.xlsx,.xls"
                                   class="@error('file') is-invalid @enderror" style="display: none;">
                        </div>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="selectedFile" style="display: none; margin-top: 12px; padding: 12px; background: #f0f0f0; border-radius: 4px;">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                        <span id="fileName"></span>
                    </div>

                    <div class="card-footer" style="margin: 24px -16px -16px -16px; padding: 16px;">
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-default">Cancel</a>
                        <button type="submit" class="btn btn-primary float-right" id="submitBtn" disabled>
                            <i class="fas fa-upload"></i> Import Questions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Subject data mapped by grade
    const subjectsByGrade = {
        'O Level': ['Biology', 'Chemistry', 'General Science', 'Mathematics', 'Physics'],
        'A Level': ['Chemistry', 'Physics'],
        '10th Grade': ['Computer Science'],
        '9th Grade': ['Mathematics'],
        '8th Grade': ['General Science']
    };

    document.addEventListener('DOMContentLoaded', function() {
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('file');
        const selectedFileDiv = document.getElementById('selectedFile');
        const fileNameSpan = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');
        const importForm = document.getElementById('importForm');
        const gradeSelect = document.getElementById('grade');
        const subjectSelect = document.getElementById('subject');

        if (!uploadZone || !fileInput) {
            console.error('Upload elements not found');
            return;
        }

        // Grade change handler - populate subjects
        if (gradeSelect && subjectSelect) {
            gradeSelect.addEventListener('change', function() {
                const selectedGrade = this.value;
                subjectSelect.innerHTML = '';

                if (!selectedGrade) {
                    subjectSelect.innerHTML = '<option value="">— Select Grade First —</option>';
                    subjectSelect.disabled = true;
                    return;
                }

                subjectSelect.disabled = false;
                const subjects = subjectsByGrade[selectedGrade] || [];

                if (subjects.length === 0) {
                    subjectSelect.innerHTML = '<option value="">— No subjects available —</option>';
                    subjectSelect.disabled = true;
                    return;
                }

                // Add default option
                let html = '<option value="">— Select Subject —</option>';

                // Add subjects
                subjects.forEach(subject => {
                    const isSelected = '{{ old('subject') }}' === subject ? 'selected' : '';
                    html += `<option value="${subject}" ${isSelected}>${subject}</option>`;
                });

                subjectSelect.innerHTML = html;
            });

            // Trigger change event on page load if grade was previously selected
            if (gradeSelect.value) {
                gradeSelect.dispatchEvent(new Event('change'));
            }
        }

        // Click to upload
        uploadZone.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                fileNameSpan.textContent = file.name + ' (' + sizeMB + ' MB)';
                selectedFileDiv.style.display = 'block';
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            }
        });

        // Drag and drop
        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadZone.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files && files[0]) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        });

        // Form validation
        if (importForm) {
            importForm.addEventListener('submit', function(e) {
                if (!fileInput.value) {
                    e.preventDefault();
                    alert('Please select a file to import');
                    return false;
                }

                if (!gradeSelect || !gradeSelect.value) {
                    e.preventDefault();
                    alert('Please select a grade');
                    return false;
                }

                if (!subjectSelect || !subjectSelect.value) {
                    e.preventDefault();
                    alert('Please select a subject');
                    return false;
                }
            });
        }
    });
</script>
@endsection
