<?php

namespace Src\Users\Infrastructure\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Src\Shared\Application\Services\AuditService;
use Src\Users\Application\Services\UsersService;
use Src\Users\Domain\Exceptions\UserDeletionException;
use Src\Users\Domain\Exceptions\UserEmailAlreadyExistsException;
use Src\Users\Domain\Exceptions\UserNotFoundException;
use Src\Users\Infrastructure\Http\Requests\CreateUsersRequest;
use Src\Users\Infrastructure\Http\Requests\UpdateUsersRequest;
use Src\Users\Infrastructure\Http\Requests\DeleteUsersRequest;

class UsersController 
{
    /**
     * Constructor del controlador
     * 
     * @param UsersService $usersService Servicio de usuarios
     * @param AuditService $auditService Servicio de auditoría
     */
    public function __construct(
        private readonly UsersService $usersService,
        private readonly AuditService $auditService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $users = $this->usersService->getAllUsers();
        
        return Inertia::render('Users/Index', [
            'users' => array_map(function ($user) {
                return $user->toArray();
            }, $users),
        ]);
    }

    /**
     * Mostrar el formulario para crear un nuevo usuario
     * 
     * @return Response
     */
    public function create(): Response
    {
        $roles = $this->usersService->getAvailableRoles();
        
        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Almacenar un nuevo usuario en la base de datos
     * 
     * @param CreateUsersRequest $request Request validado
     * @return RedirectResponse
     */
    public function store(CreateUsersRequest $request): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'CreateUsersUseCase';
        $requestData = $request->validated();

        try {
            $data = $requestData;
            $data['created_by'] = Auth::id();

            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $user = $this->usersService->createUser($data);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario creado exitosamente.');
        } catch (UserEmailAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'error_reason' => 'Email ya existe',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['email' => $e->getMessage()]);
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
                ->withErrors(['error' => 'Ocurrió un error al crear el usuario.']);
        }
    }

    /**
     * Mostrar información de un usuario específico
     * 
     * @param int $user ID del usuario (desde la ruta {user})
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $user): \Illuminate\Http\JsonResponse
    {
        $userEntity = $this->usersService->getUserById($user);

        if (!$userEntity) {
            throw new UserNotFoundException($user);
        }

        return response()->json([
            'user' => $userEntity->toArray(),
        ]);
    }

    /**
     * Mostrar el formulario para editar un usuario
     * 
     * @param int $user ID del usuario (desde la ruta {user})
     * @return Response
     */
    public function edit(int $user): Response
    {
        $userEntity = $this->usersService->getUserById($user);

        if (!$userEntity) {
            throw new UserNotFoundException($user);
        }

        $roles = $this->usersService->getAvailableRoles();
        
        // Obtener IDs de roles actuales del usuario
        $currentRoleIds = null;
        if ($userEntity->roles !== null && count($userEntity->roles) > 0) {
            $currentRoleIds = array_map(function ($role) {
                return $role->id;
            }, $userEntity->roles);
        }

        return Inertia::render('Users/Edit', [
            'user' => $userEntity->toArray(),
            'roles' => $roles,
            'currentRoleIds' => $currentRoleIds,
        ]);
    }

    /**
     * Actualizar un usuario existente
     * 
     * @param UpdateUsersRequest $request Request validado
     * @param int $user ID del usuario a actualizar (desde la ruta {user})
     * @return RedirectResponse
     */
    public function update(UpdateUsersRequest $request, int $user): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'UpdateUsersUseCase';
        $requestData = $request->validated();

        try {
            $data = $requestData;
            
            // Si no se proporciona contraseña, no incluirla en la actualización
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'user_id' => $user,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $userEntity = $this->usersService->updateUser($user, $data);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'user_id' => $userEntity->id,
                    'user_name' => $userEntity->name,
                    'user_email' => $userEntity->email,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (UserNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'error_reason' => 'Usuario no encontrado',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (UserEmailAlreadyExistsException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'error_reason' => 'Email ya existe',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['email' => $e->getMessage()]);
        } catch (\Exception $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al actualizar el usuario.']);
        }
    }

    /**
     * Eliminar un usuario
     * 
     * @param DeleteUsersRequest $request Request validado
     * @param int $user ID del usuario a eliminar (desde la ruta {user})
     * @return RedirectResponse
     */
    public function destroy(DeleteUsersRequest $request, int $user): RedirectResponse
    {
        $functionName = __FUNCTION__;
        $controllerName = self::class;
        $useCaseName = 'DeleteUsersUseCase';
        $requestData = $request->validated();

        try {
            $this->auditService->log('info', 'INIT', $controllerName, $useCaseName, $functionName, [
                'request_data' => $requestData,
                'user_id' => $user,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $this->usersService->deleteUser($user);

            $this->auditService->logSuccess(
                $controllerName,
                $useCaseName,
                $functionName,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'ip_address' => $request->ip(),
                ]
            );

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario eliminado exitosamente.');
        } catch (UserNotFoundException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'error_reason' => 'Usuario no encontrado',
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (UserDeletionException $e) {
            $this->auditService->logError(
                $controllerName,
                $useCaseName,
                $functionName,
                $e,
                [
                    'request_data' => $requestData,
                    'user_id' => $user,
                    'error_reason' => 'Error al eliminar usuario',
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
                    'user_id' => $user,
                    'ip_address' => $request->ip(),
                ]
            );
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Ocurrió un error al eliminar el usuario.']);
        }
    }
}
