<?php

namespace Src\Roles\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Src\Roles\Application\Services\RolesService;
use Src\Roles\Domain\Exceptions\RoleDeletionException;
use Src\Roles\Domain\Exceptions\RoleInUseException;
use Src\Roles\Domain\Exceptions\RoleNotFoundException;
use Src\Roles\Domain\Exceptions\RoleSlugAlreadyExistsException;
use Src\Roles\Infrastructure\Http\Requests\CreateRolesRequest;
use Src\Roles\Infrastructure\Http\Requests\UpdateRolesRequest;
use Src\Roles\Infrastructure\Http\Requests\DeleteRolesRequest;
use Src\Shared\Application\Services\AuditService;

class RolesController 
{
    /**
     * Constructor del controlador
     * 
     * @param RolesService $rolesService Servicio de roles
     * @param AuditService $auditService Servicio de auditoría
     */
    public function __construct(
        private readonly RolesService $rolesService,
        private readonly AuditService $auditService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $roles = $this->rolesService->getAllRoles();
        
        return Inertia::render('Roles/Index', [
            'roles' => array_map(function ($role) {
                return $role->toArray();
            }, $roles),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo rol
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    /**
     * Almacenar un nuevo rol en la base de datos
     * 
     * @param CreateRolesRequest $request Request validado
     * @return RedirectResponse
     */
    public function store(CreateRolesRequest $request): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'CreateRolesUseCase';
        $requestData = $request->validated();

        try {
            $data = $requestData;
            $data['created_by'] = Auth::id();

            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $role = $this->rolesService->createRole($data);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol creado exitosamente.');
        } catch (RoleSlugAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'error_reason' => 'Slug ya existe',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['slug' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al crear el rol.']);
        }
    }

    /**
     * Mostrar información de un rol específico
     * 
     * @param int $role ID del rol (desde la ruta {role})
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $role): \Illuminate\Http\JsonResponse
    {
        $roleEntity = $this->rolesService->getRoleById($role);

        if (!$roleEntity) {
            throw new RoleNotFoundException($role);
        }

        return response()->json([
            'role' => $roleEntity->toArray(),
        ]);
    }

    /**
     * Mostrar el formulario para editar un rol
     * 
     * @param int $role ID del rol (desde la ruta {role})
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(int $role): \Illuminate\Http\JsonResponse
    {
        $roleEntity = $this->rolesService->getRoleById($role);

        if (!$roleEntity) {
            throw new RoleNotFoundException($role);
        }

        return response()->json([
            'role' => $roleEntity->toArray(),
        ]);
    }

    /**
     * Actualizar un rol existente
     * 
     * @param UpdateRolesRequest $request Request validado
     * @param int $role ID del rol a actualizar (desde la ruta {role})
     * @return RedirectResponse
     */
    public function update(UpdateRolesRequest $request, int $role): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'UpdateRolesUseCase';
        $requestData = $request->validated();

        try {
            $data = $requestData;

            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'role_id' => $role,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $roleEntity = $this->rolesService->updateRole($role, $data);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'role_id' => $roleEntity->id,
                    'role_name' => $roleEntity->name,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol actualizado exitosamente.');
        } catch (RoleNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'error_reason' => 'Rol no encontrado',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (RoleSlugAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'error_reason' => 'Slug ya existe',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['slug' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al actualizar el rol.']);
        }
    }

    /**
     * Eliminar un rol
     * 
     * @param DeleteRolesRequest $request Request validado
     * @param int $role ID del rol a eliminar (desde la ruta {role})
     * @return RedirectResponse
     */
    public function destroy(DeleteRolesRequest $request, int $role): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'DeleteRolesUseCase';
        $requestData = $request->validated();

        try {
            $deletion_reason = $requestData['deletion_reason'];

            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'role_id' => $role,
                'deletion_reason' => $deletion_reason,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->rolesService->deleteRole($role, $deletion_reason);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'deletion_reason' => $deletion_reason,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol eliminado exitosamente.');
        } catch (RoleNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'error_reason' => 'Rol no encontrado',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (RoleInUseException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'users_count' => $e->getUsersCount(),
                    'error_reason' => 'Rol en uso por usuarios',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (RoleDeletionException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'error_reason' => 'Error al eliminar rol',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'role_id' => $role,
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al eliminar el rol.']);
        }
    }

    public function test()
    {
        dd('test');
    }

    /**
     * Asignar un rol a un usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function assignRoleToUser(Request $request)
    {
        return view('roles.assignRoleToUser'); // TODO: es vuejs
    }

    /**
     * Remover un rol de un usuario
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeRoleFromUser(Request $request)
    {
        return view('roles.removeRoleFromUser'); // TODO: es vuejs
    }

    /**
     * Obtener los roles de un usuario
     *
     * @param int $userId
     * @return \Illuminate\Http\Response
     */
    public function getUserRoles($userId)
    {
        dd('getUserRoles');
    }

    /**
     * Obtener los usuarios de un rol
     *
     * @param int $roleId
     * @return \Illuminate\Http\Response
     */
    public function getRoleUsers($roleId)
    {
        dd('getRoleUsers');
    }
}
