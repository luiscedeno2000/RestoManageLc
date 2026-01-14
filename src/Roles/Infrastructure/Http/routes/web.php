<?php

use Illuminate\Support\Facades\Route;
use Src\Roles\Infrastructure\Http\Controllers\RolesController;

Route::prefix('v1')->group(function () {
    Route::prefix('configurations')->group(function () {
        Route::resource('roles', RolesController::class);
        // Rutas para roles x users
        Route::post('roles/assign-role-to-user', [RolesController::class, 'assignRoleToUser'])->name('roles.assignRoleToUser');
        Route::post('roles/remove-role-from-user', [RolesController::class, 'removeRoleFromUser'])->name('roles.removeRoleFromUser');
        Route::get('roles/user/{userId}/roles', [RolesController::class, 'getUserRoles'])->name('roles.getUserRoles');
        Route::get('roles/{roleId}/users', [RolesController::class, 'getRoleUsers'])->name('roles.getRoleUsers');
    });
});

