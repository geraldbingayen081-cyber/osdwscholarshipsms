<?php

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\AdminComplianceController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\GranteeRequirementController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ScholarController as AdminScholarController;
use App\Http\Controllers\Admin\ScholarshipController;
use App\Http\Controllers\Admin\ScholarshipRequirementController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Student\ApplicationController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\ScholarRenewalController;
use App\Http\Controllers\Student\StudentComplianceController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\StudentScholarshipController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Root Landing Route
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'destroy'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Portal Routes (role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Academic Year Management
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::post('/academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // Semester Management
    Route::get('/semesters', [SemesterController::class, 'index'])->name('semesters.index');
    Route::get('/semesters/create', [SemesterController::class, 'create'])->name('semesters.create');
    Route::post('/semesters', [SemesterController::class, 'store'])->name('semesters.store');
    Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])->name('semesters.edit');
    Route::put('/semesters/{semester}', [SemesterController::class, 'update'])->name('semesters.update');

    // Scholarship Management
    Route::get('/scholarships', [ScholarshipController::class, 'index'])->name('scholarships.index');
    Route::get('/scholarships/create', [ScholarshipController::class, 'create'])->name('scholarships.create');
    Route::post('/scholarships', [ScholarshipController::class, 'store'])->name('scholarships.store');
    Route::get('/scholarships/{scholarship}', [ScholarshipController::class, 'show'])->name('scholarships.show');
    Route::get('/scholarships/{scholarship}/edit', [ScholarshipController::class, 'edit'])->name('scholarships.edit');
    Route::put('/scholarships/{scholarship}', [ScholarshipController::class, 'update'])->name('scholarships.update');
    Route::delete('/scholarships/{scholarship}', [ScholarshipController::class, 'destroy'])->name('scholarships.destroy');
    Route::patch('/scholarships/{scholarship}/status', [ScholarshipController::class, 'updateStatus'])->name('scholarships.update-status');

    // Requirement Management
    Route::post('/scholarships/{scholarship}/requirements', [ScholarshipRequirementController::class, 'store'])->name('scholarship-requirements.store');
    Route::put('/scholarship-requirements/{requirement}', [ScholarshipRequirementController::class, 'update'])->name('scholarship-requirements.update');
    Route::delete('/scholarship-requirements/{requirement}', [ScholarshipRequirementController::class, 'destroy'])->name('scholarship-requirements.destroy');

    // Applications Management
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::post('/applications/bulk-status', [AdminApplicationController::class, 'bulkUpdateStatus'])->name('applications.bulk-status');
    Route::post('/documents/{document}/verify', [AdminApplicationController::class, 'verifyDocument'])->name('applications.documents.verify');
    Route::get('/documents/{document}/download', [AdminApplicationController::class, 'downloadDocument'])->name('applications.documents.download');

    // Scholars Management
    Route::get('/scholars', [AdminScholarController::class, 'index'])->name('scholars.index');
    Route::get('/scholars/{scholar}', [AdminScholarController::class, 'show'])->name('scholars.show');
    Route::post('/scholars/{scholar}/status', [AdminScholarController::class, 'updateStatus'])->name('scholars.update-status');
    Route::get('/renewals/{renewal}/download', [AdminScholarController::class, 'downloadRenewalDoc'])->name('scholars.renewals.download');
    Route::post('/renewals/{renewal}/verify', [AdminScholarController::class, 'verifyRenewalDoc'])->name('scholars.renewals.verify');

    // Grantees Requirements Management
    Route::get('/grantees-requirements', [GranteeRequirementController::class, 'index'])->name('grantees-requirements.index');
    Route::post('/grantees-requirements/requirements', [GranteeRequirementController::class, 'storeRequirement'])->name('grantees-requirements.store-requirement');
    Route::put('/grantees-requirements/requirements/{requirement}', [GranteeRequirementController::class, 'updateRequirement'])->name('grantees-requirements.update-requirement');
    Route::patch('/grantees-requirements/requirements/{requirement}/status', [GranteeRequirementController::class, 'updateStatus'])->name('grantees-requirements.update-status');
    Route::delete('/grantees-requirements/requirements/{requirement}', [GranteeRequirementController::class, 'destroyRequirement'])->name('grantees-requirements.destroy-requirement');
    Route::post('/grantees-requirements/request', [GranteeRequirementController::class, 'requestRequirements'])->name('grantees-requirements.request');
    Route::post('/grantees-requirements/renewals/{renewal}/verify', [GranteeRequirementController::class, 'verifyRenewal'])->name('grantees-requirements.verify-renewal');

    // Compliance Requests (Active Scholars Periodic Compliance)
    Route::get('/compliance', [AdminComplianceController::class, 'index'])->name('compliance.index');
    Route::post('/compliance', [AdminComplianceController::class, 'store'])->name('compliance.store');
    Route::get('/compliance/{complianceRequest}', [AdminComplianceController::class, 'show'])->name('compliance.show');
    Route::patch('/compliance/{complianceRequest}/extend', [AdminComplianceController::class, 'extendDeadline'])->name('compliance.extend');
    Route::delete('/compliance/{complianceRequest}', [AdminComplianceController::class, 'destroy'])->name('compliance.destroy');
    Route::post('/compliance-documents/{document}/verify', [AdminComplianceController::class, 'verifyDocument'])->name('compliance.documents.verify');
    Route::get('/compliance-documents/{document}/view', [AdminComplianceController::class, 'viewDocument'])->name('compliance.documents.view');

    // Students Management
    Route::get('/students', [StudentManagementController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentManagementController::class, 'show'])->name('students.show');

    // Reports & Analytics
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');

    // System Settings & Admin Profile
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [\App\Http\Controllers\Admin\SettingController::class, 'updateProfile'])->name('settings.update-profile');
    Route::post('/settings/system', [\App\Http\Controllers\Admin\SettingController::class, 'updateSystem'])->name('settings.update-system');

    // Data Exports (CSV)
    Route::get('/export/scholars', [\App\Http\Controllers\Admin\ExportController::class, 'exportScholars'])->name('export.scholars');
    Route::get('/export/applications', [\App\Http\Controllers\Admin\ExportController::class, 'exportApplications'])->name('export.applications');
});

/*
|--------------------------------------------------------------------------
| Student Portal Routes (role: student)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Profile Management
    Route::get('/profile', [StudentProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [StudentProfileController::class, 'update'])->name('profile.update');

    // Available Scholarships
    Route::get('/scholarships', [StudentScholarshipController::class, 'index'])->name('scholarships.index');
    Route::get('/scholarships/{scholarship}', [StudentScholarshipController::class, 'show'])->name('scholarships.show');

    // Applications Submission & Document Uploads
    Route::get('/scholarships/{scholarship}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/scholarships/{scholarship}/apply', [ApplicationController::class, 'store'])->name('applications.store');

    // My Applications Tracking
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');

    // Document Resubmission & Deficiency Fast Resolution
    Route::get('/applications/{application}/resolve', [ApplicationController::class, 'resolveDeficiency'])->name('applications.resolve');
    Route::post('/applications/{application}/resolve', [ApplicationController::class, 'updateDeficiency'])->name('applications.resolve.update');
    Route::get('/documents/{document}/view', [ApplicationController::class, 'viewSecureDocument'])->name('documents.view');
    Route::post('/documents/{document}/resubmit', [ApplicationController::class, 'resubmitDocument'])->name('documents.resubmit');

    // Active Scholars Compliance Submission
    Route::get('/compliance', [StudentComplianceController::class, 'index'])->name('compliance.index');
    Route::get('/compliance/{complianceRequest}', [StudentComplianceController::class, 'show'])->name('compliance.show');
    Route::post('/compliance/{complianceRequest}/requirements/{requirement}', [StudentComplianceController::class, 'submitDocument'])->name('compliance.documents.submit');
    Route::get('/compliance-documents/{document}/view', [StudentComplianceController::class, 'viewDocument'])->name('compliance.documents.view');

    // Scholarship Renewal Submission
    Route::post('/scholars/{scholar}/renew', [ScholarRenewalController::class, 'store'])->name('scholars.renew');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});
