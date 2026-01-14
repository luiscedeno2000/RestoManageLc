<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import CreateRoleDialog from './components/CreateRoleDialog.vue';
import ViewRoleDialog from './components/ViewRoleDialog.vue';
import EditRoleDialog from './components/EditRoleDialog.vue';
import DeleteRoleDialog from './components/DeleteRoleDialog.vue';
import RolesTable from './components/RolesTable.vue';

/**
 * Props recibidos desde el controlador
 */
defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
});

/**
 * Estado para controlar la visibilidad de los diálogos
 */
const visibleCreateDialog = ref(false);
const visibleViewDialog = ref(false);
const visibleEditDialog = ref(false);
const visibleDeleteDialog = ref(false);
const selectedRoleId = ref(null);
const selectedRoleName = ref('');

/**
 * Abrir el diálogo de creación
 */
const openCreateDialog = () => {
    visibleCreateDialog.value = true;
};

/**
 * Abrir el diálogo de visualización
 */
const openViewDialog = (roleId) => {
    selectedRoleId.value = roleId;
    visibleViewDialog.value = true;
};

/**
 * Abrir el diálogo de edición
 */
const openEditDialog = (roleId) => {
    selectedRoleId.value = roleId;
    visibleEditDialog.value = true;
};

/**
 * Manejar el evento de éxito después de crear/actualizar el rol
 */
const handleSuccess = () => {
    // El diálogo se cierra automáticamente
    // La tabla se actualizará automáticamente con Inertia
};

/**
 * Abrir el diálogo de eliminación
 */
const openDeleteDialog = (roleId, roleName) => {
    selectedRoleId.value = roleId;
    selectedRoleName.value = roleName;
    visibleDeleteDialog.value = true;
};
</script>

<template>
    <AppLayout title="Roles">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Roles
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <!-- Botón Crear Rol -->
                        <div class="mb-4 flex justify-end">
                            <Button 
                                label="Crear Rol"
                                icon="pi pi-plus"
                                severity="success"
                                @click="openCreateDialog"
                            />
                        </div>

                        <RolesTable 
                            :roles="roles"
                            @view="openViewDialog"
                            @edit="openEditDialog"
                            @delete="openDeleteDialog"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Dialog para Crear Rol -->
        <CreateRoleDialog 
            v-model:visible="visibleCreateDialog"
            @success="handleSuccess"
        />

        <!-- Dialog para Ver Rol -->
        <ViewRoleDialog 
            v-model:visible="visibleViewDialog"
            :role-id="selectedRoleId"
        />

        <!-- Dialog para Editar Rol -->
        <EditRoleDialog 
            v-model:visible="visibleEditDialog"
            :role-id="selectedRoleId"
            @success="handleSuccess"
        />

        <!-- Dialog para Eliminar Rol -->
        <DeleteRoleDialog 
            v-model:visible="visibleDeleteDialog"
            :role-id="selectedRoleId"
            :role-name="selectedRoleName"
            @success="handleSuccess"
        />
    </AppLayout>
</template>
