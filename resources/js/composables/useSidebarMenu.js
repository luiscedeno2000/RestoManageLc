/**
 * Composable para gestionar el menú del Sidebar
 * 
 * Este composable proporciona la lógica para:
 * - Detectar el contexto actual basado en la ruta
 * - Obtener los items del menú correspondientes
 * - Marcar el item activo
 * 
 * Sigue principios de Clean Architecture y DDD:
 * - Separación de responsabilidades
 * - Lógica reutilizable
 * - Fácil de testear
 */

import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { getMenuContext, getMenuForContext } from '@/config/menus';

/**
 * Composable para gestionar el menú del Sidebar
 * 
 * @returns {Object} - Objeto con items del menú y utilidades
 */
export function useSidebarMenu() {
    const page = usePage();
    
    /**
     * Obtiene el nombre de la ruta actual
     * 
     * @returns {string} - Nombre de la ruta actual o 'default'
     */
    const currentRouteName = computed(() => {
        try {
            // Usar Ziggy para obtener la ruta actual
            if (typeof window.route !== 'undefined' && typeof window.route().current === 'function') {
                const current = window.route().current();
                return current || 'default';
            }
            return 'default';
        } catch (error) {
            console.warn('No se pudo obtener la ruta actual:', error);
            return 'default';
        }
    });

    /**
     * Obtiene el contexto del menú basado en la ruta actual
     * 
     * @returns {string} - Contexto del menú
     */
    const menuContext = computed(() => {
        const routeName = currentRouteName.value;
        return getMenuContext(routeName);
    });

    /**
     * Obtiene los items del menú para el contexto actual
     * 
     * @returns {Array<MenuItem>} - Items del menú con estado activo actualizado
     */
    const menuItems = computed(() => {
        const context = menuContext.value;
        const items = getMenuForContext(context);
        const currentRoute = currentRouteName.value;

        // Marcar el item activo
        return items.map(item => ({
            ...item,
            active: item.route === currentRoute || 
                   (item.route.endsWith('.*') && currentRoute.startsWith(item.route.replace('.*', ''))) ||
                   currentRoute.includes(item.route),
        }));
    });

    /**
     * Verifica si un item del menú está activo
     * 
     * @param {string} route - Nombre de la ruta a verificar
     * @returns {boolean} - True si la ruta está activa
     */
    const isActive = (route) => {
        const currentRoute = currentRouteName.value;
        return currentRoute === route || 
               currentRoute.includes(route);
    };

    return {
        menuItems,
        menuContext,
        currentRouteName,
        isActive,
    };
}
