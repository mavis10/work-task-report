<?php

use App\Http\Controllers\Api\Reports\WorkTaskReportController;
use Illuminate\Support\Facades\Route;


Route::get('/reports/work-tasks/resolutions', [WorkTaskReportController::class, 'resolutions']);

// authentication can be added to provide more security
// Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function(){
    //Route::get('/reports/work-tasks/resolutions', [WorkTaskReportController::class, 'resolutions']);
// });



