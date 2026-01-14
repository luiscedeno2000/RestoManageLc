<?php

namespace Src\Admin\Infrastructure\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Src\Admin\Application\Services\AdminService;
use Src\Shared\Application\Services\AuditService;

class AdminController 
{
    /**
     * Constructor del controlador
     * 
     * @param AdminService $adminService Servicio de administración
     * @param AuditService $auditService Servicio de auditoría
     */
    public function __construct(
        private readonly AdminService $adminService,
        private readonly AuditService $auditService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'AdminService';

        try {
            $user = auth()->user();
            
            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => $user?->id,
            ]);
            
            if (!$user) {
                $this->auditService->logFailure(
                    $controllerName,
                    $useCaseName,
                    $functionName,
                    'Usuario no autenticado',
                    ['ip_address' => $request->ip()]
                );
                return Inertia::render('Admin/Index', [
                    'admin' => [
                        'id' => null,
                        'name' => '',
                        'email' => '',
                        'state' => false,
                        'roles' => [],
                    ],
                    'allRoles' => [],
                ]);
            }
            
            // Obtener información del administrador actual
            $adminInfo = $this->adminService->getAdminInfo($user->id);
            
            // Obtener todos los roles disponibles en el sistema
            $allRoles = $this->adminService->getAllRoles();
            
            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'user_id' => $user->id,
                    'admin_id' => $adminInfo['id'],
                    'roles_count' => count($adminInfo['roles'] ?? []),
                    'all_roles_count' => count($allRoles),
                    'ip_address' => $request->ip(),
                ]
            );
            
            return Inertia::render('Admin/Index', [
                'admin' => $adminInfo,
                'allRoles' => $allRoles,
            ]);
        } catch (\Throwable $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'ip_address' => $request->ip(),
                    'user_id' => auth()->id(),
                ]
            );
            throw $e;
        }
    }

    public function create()
    {
        return view('users.create'); // TODO: es vuejs
    }

    public function store(Request $request) 
    {
        return view('users.store'); // TODO: es vuejs
    }

    public function show($id)
    {
        dd('show');
    }

    public function edit($id)
    {
        return view('users.edit'); // TODO: es vuejs
    }

    public function update(Request $request, $id)
    {
        return view('users.update'); // TODO: es vuejs
    }

    public function destroy($id)
    {
        return view('users.destroy'); // TODO: es vuejs
    }

    public function test()
    {
        dd('test');
    }
}