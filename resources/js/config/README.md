# Configuración de Menús del Sidebar

Este directorio contiene la configuración centralizada de menús del Sidebar, siguiendo principios de DDD (Domain-Driven Design).

## Estructura

- `menus.js` - Configuración de menús por módulo/contexto

## Cómo funciona

### 1. Configuración de Menús (`menus.js`)

Define los menús disponibles para cada contexto/módulo:

```javascript
export const menuConfig = {
    admin: [
        {
            label: 'Usuarios',
            route: 'users.index',
            icon: 'pi pi-users',
        },
        // ...
    ],
};
```

### 2. Mapeo de Rutas a Contextos

Define qué menú mostrar según la ruta actual:

```javascript
export const routeContextMap = {
    'admin.*': 'admin',
    'users.*': 'admin',
    'roles.*': 'admin',
};
```

### 3. Composable (`useSidebarMenu.js`)

Proporciona la lógica reactiva para:
- Detectar el contexto actual
- Obtener los items del menú
- Marcar el item activo

## Agregar un nuevo módulo

1. **Agregar el menú en `menus.js`:**

```javascript
export const menuConfig = {
    nuevoModulo: [
        {
            label: 'Nuevo Item',
            route: 'nuevo.item',
            icon: 'pi pi-icon',
        },
    ],
};
```

2. **Agregar el mapeo de rutas:**

```javascript
export const routeContextMap = {
    'nuevo.*': 'nuevoModulo',
};
```

3. **El Sidebar se actualizará automáticamente**

## Ventajas de este enfoque

- ✅ **Escalable**: Agregar nuevos módulos es simple
- ✅ **Mantenible**: Configuración centralizada
- ✅ **Orientado a DDD**: Cada módulo define sus propios enlaces
- ✅ **Type-safe**: Fácil de extender con TypeScript
- ✅ **Testeable**: Lógica separada y reutilizable
