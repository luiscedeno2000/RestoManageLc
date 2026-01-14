<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

/**
 * Props recibidos desde el controlador
 */
defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Usuarios
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <DataTable 
                            :value="users" 
                            :paginator="true" 
                            :rows="10"
                            :rowsPerPageOptions="[5, 10, 20, 50]"
                            paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                            currentPageReportTemplate="{first} a {last} de {totalRecords}"
                            responsiveLayout="scroll"
                            class="p-datatable-sm datatable-white"
                        >
                            <!-- Columna: Nombre -->
                            <Column field="name" header="Nombre" :sortable="true">
                                <template #body="slotProps">
                                    <span>{{ slotProps.data.name }}</span>
                                </template>
                            </Column>

                            <!-- Columna: Email -->
                            <Column field="email" header="Email" :sortable="true">
                                <template #body="slotProps">
                                    <span>{{ slotProps.data.email }}</span>
                                </template>
                            </Column>

                            <!-- Columna: Estado -->
                            <Column field="state" header="Estado" :sortable="true">
                                <template #body="slotProps">
                                    <span 
                                        :class="[
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            slotProps.data.state 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ slotProps.data.state ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </template>
                            </Column>

                            <!-- Columna: Roles -->
                            <Column field="roles" header="Roles" :sortable="false">
                                <template #body="slotProps">
                                    <div v-if="slotProps.data.roles && slotProps.data.roles.length > 0" class="flex flex-wrap gap-1">
                                        <span
                                            v-for="role in slotProps.data.roles"
                                            :key="role.id"
                                            class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                                        >
                                            {{ role.name }}
                                        </span>
                                    </div>
                                    <span v-else class="text-gray-400 text-sm">Sin roles</span>
                                </template>
                            </Column>

                            <!-- Columna: Acciones -->
                            <Column header="Acciones" :exportable="false" style="min-width: 8rem">
                                <template #body="slotProps">
                                    <div class="flex gap-2">
                                        <!-- Botón Ver -->
                                        <Button 
                                            icon="pi pi-eye" 
                                            severity="info"
                                            size="small"
                                            rounded
                                            text
                                            title="Ver"
                                        />

                                        <!-- Botón Editar -->
                                        <Button 
                                            icon="pi pi-pencil" 
                                            severity="warning"
                                            size="small"
                                            rounded
                                            text
                                            title="Editar"
                                            @click="$inertia.visit(route('users.edit', slotProps.data.id))"
                                        />

                                        <!-- Botón Eliminar -->
                                        <Button 
                                            icon="pi pi-trash" 
                                            severity="danger"
                                            size="small"
                                            rounded
                                            text
                                            title="Eliminar"
                                        />
                                    </div>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
