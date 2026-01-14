<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from 'primevue/card';

/**
 * Props recibidos desde el controlador
 */
defineProps({
    admin: {
        type: Object,
        default: () => ({
            id: null,
            name: '',
            email: '',
            state: false,
            roles: [],
        }),
    },
    allRoles: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout title="Admin">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Administrador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Información del Administrador -->
                <Card class="mb-6">
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-user text-2xl text-blue-600"></i>
                            <span>Información del Administrador</span>
                        </div>
                    </template>
                    <template #content>
                        <div class="space-y-4">
                            <!-- Nombre -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-semibold text-gray-700">Nombre:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-gray-900">{{ admin.name }}</span>
                                </div>
                            </div>

                            <!-- Correo -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-semibold text-gray-700">Correo:</span>
                                </div>
                                <div class="flex-1">
                                    <span class="text-gray-900">{{ admin.email }}</span>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-semibold text-gray-700">Estado:</span>
                                </div>
                                <div class="flex-1">
                                    <span 
                                        :class="[
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            admin.state 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ admin.state ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Roles Asignados -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-32">
                                    <span class="text-sm font-semibold text-gray-700">Roles Asignados:</span>
                                </div>
                                <div class="flex-1">
                                    <div v-if="admin.roles && admin.roles.length > 0" class="flex flex-wrap gap-2">
                                        <span
                                            v-for="role in admin.roles"
                                            :key="role.id"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
                                        >
                                            <i class="pi pi-shield mr-2"></i>
                                            {{ role.name }}
                                        </span>
                                    </div>
                                    <p v-else class="text-sm text-gray-500">
                                        No tiene roles asignados.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </Card>

                <!-- Lista de Roles Disponibles -->
                <Card>
                    <template #title>
                        <div class="flex items-center gap-3">
                            <i class="pi pi-list text-2xl text-green-600"></i>
                            <span>Roles Disponibles en el Sistema</span>
                        </div>
                    </template>
                    <template #content>
                        <div v-if="allRoles && allRoles.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="role in allRoles"
                                :key="role.id"
                                class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <i 
                                            :class="[
                                                'pi pi-shield text-2xl',
                                                role.state ? 'text-blue-600' : 'text-gray-400'
                                            ]"
                                        ></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="font-semibold text-gray-900">{{ role.name }}</h3>
                                            <span 
                                                v-if="!role.state"
                                                class="px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800"
                                            >
                                                Inactivo
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ role.slug }}</span>
                                        </p>
                                        <p v-if="role.description" class="text-sm text-gray-500">{{ role.description }}</p>
                                        <p v-else class="text-sm text-gray-400 italic">Sin descripción</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8 text-gray-500">
                            <i class="pi pi-info-circle text-3xl mb-2"></i>
                            <p>No hay roles disponibles en el sistema.</p>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>