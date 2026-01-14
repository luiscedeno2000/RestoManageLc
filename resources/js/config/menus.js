/**
 * Configuración de Menús del Sidebar
 * 
 * Este archivo centraliza la configuración de menús por módulo/contexto.
 * Sigue principios DDD donde cada módulo define sus propios enlaces de navegación.
 * 
 * Estructura:
 * - Cada módulo tiene su propia sección de menú
 * - Los enlaces se organizan por contexto (Admin, Dashboard, etc.)
 * - Es escalable: agregar nuevos módulos solo requiere agregar su configuración aquí
 */

/**
 * Configuración de menús por contexto/módulo
 * 
 * @type {Object<string, Array<MenuItem>>}
 */
export const menuConfig = {
    /**
     * Menú para el contexto de Administración
     * Se muestra cuando el usuario está en páginas relacionadas con Admin
     */
    admin: [
        {
            label: 'Admin',
            route: 'admin.index',
            icon: 'pi pi-home',
            active: false,
        },
        {
            label: 'Usuarios',
            route: 'users.index',
            icon: 'pi pi-users',
            active: false,
        },
        {
            label: 'Roles',
            route: 'roles.index',
            icon: 'pi pi-shield',
            active: false,
        },
    ],

    /**
     * Menú para el contexto de Dashboard
     * Se muestra cuando el usuario está en el dashboard principal
     */
    dashboard: [
        {
            label: 'Dashboard',
            route: 'dashboard',
            icon: 'pi pi-home',
            active: false,
        },
        {
            label: 'Inicio',
            route: 'home.index',
            icon: 'pi pi-home',
            active: false,
        },
    ],

    /**
     * Menú por defecto cuando no se detecta un contexto específico
     */
    default: [
        {
            label: 'Dashboard',
            route: 'dashboard',
            icon: 'pi pi-home',
            active: false,
        },
    ],
};

/**
 * Mapeo de rutas a contextos de menú
 * 
 * Define qué menú mostrar según la ruta actual
 * 
 * @type {Object<string, string>}
 */
export const routeContextMap = {
    // Rutas de Admin
    'admin.index': 'admin',
    'admin.create': 'admin',
    'admin.show': 'admin',
    'admin.edit': 'admin',
    'admin.store': 'admin',
    'admin.update': 'admin',
    'admin.destroy': 'admin',
    
    // Rutas de Users
    'users.index': 'admin',
    'users.create': 'admin',
    'users.show': 'admin',
    'users.edit': 'admin',
    'users.store': 'admin',
    'users.update': 'admin',
    'users.destroy': 'admin',
    'users.test': 'admin',
    
    // Rutas de Roles
    'roles.index': 'admin',
    'roles.create': 'admin',
    'roles.show': 'admin',
    'roles.edit': 'admin',
    'roles.store': 'admin',
    'roles.update': 'admin',
    'roles.destroy': 'admin',
    'roles.test': 'admin',
    'roles.assignRoleToUser': 'admin',
    'roles.removeRoleFromUser': 'admin',
    'roles.getUserRoles': 'admin',
    'roles.getRoleUsers': 'admin',
    
    // Rutas de Dashboard
    'dashboard': 'dashboard',
    
    // Rutas de Home
    'home.index': 'dashboard',
};

/**
 * Obtiene el contexto del menú basado en la ruta actual
 * 
 * @param {string} currentRoute - Nombre de la ruta actual
 * @returns {string} - Contexto del menú ('admin', 'dashboard', 'default')
 */
export function getMenuContext(currentRoute) {
    // Buscar coincidencia exacta primero
    if (routeContextMap[currentRoute]) {
        return routeContextMap[currentRoute];
    }

    // Buscar coincidencia con wildcard
    for (const [routePattern, context] of Object.entries(routeContextMap)) {
        if (routePattern.endsWith('.*')) {
            const baseRoute = routePattern.replace('.*', '');
            if (currentRoute.startsWith(baseRoute)) {
                return context;
            }
        }
    }

    return 'default';
}

/**
 * Obtiene el menú configurado para un contexto específico
 * 
 * @param {string} context - Contexto del menú
 * @returns {Array<MenuItem>} - Array de items del menú
 */
export function getMenuForContext(context) {
    return menuConfig[context] || menuConfig.default;
}

/**
 * @typedef {Object} MenuItem
 * @property {string} label - Etiqueta visible del enlace
 * @property {string} route - Nombre de la ruta de Laravel
 * @property {string} icon - Clase del icono (PrimeVue)
 * @property {boolean} active - Si el enlace está activo
 */
