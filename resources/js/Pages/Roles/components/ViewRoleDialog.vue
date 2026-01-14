<script setup>
import { ref, watch } from 'vue';
import Dialog from 'primevue/dialog';
import { router } from '@inertiajs/vue3';

/**
 * Props del componente
 */
const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    roleId: {
        type: Number,
        default: null,
    },
});

/**
 * Emits del componente
 */
const emit = defineEmits(['update:visible']);

/**
 * Estado del rol
 */
const role = ref(null);
const loading = ref(false);
const error = ref(null);

/**
 * Cerrar el diálogo
 */
const closeDialog = () => {
    emit('update:visible', false);
    role.value = null;
    error.value = null;
};

/**
 * Cargar información del rol
 */
const loadRole = async () => {
    if (!props.roleId) {
        return;
    }

    loading.value = true;
    error.value = null;

    try {
        const response = await fetch(route('roles.show', props.roleId), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Error al cargar el rol');
        }

        const data = await response.json();
        role.value = data.role;
    } catch (err) {
        error.value = err.message || 'Error al cargar la información del rol';
    } finally {
        loading.value = false;
    }
};

/**
 * Observar cambios en visible y roleId
 */
watch([() => props.visible, () => props.roleId], ([newVisible, newRoleId]) => {
    if (newVisible && newRoleId) {
        loadRole();
    }
}, { immediate: true });
</script>

<template>
    <Dialog 
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        modal 
        header="Información del Rol"
        :style="{ width: '50rem' }"
        :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
    >
        <div v-if="loading" class="flex justify-center items-center py-8">
            <i class="pi pi-spin pi-spinner text-2xl text-gray-500"></i>
        </div>

        <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800">{{ error }}</p>
        </div>

        <div v-else-if="role" class="space-y-6">
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Rol
                </label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-900 font-semibold">{{ role.name }}</p>
                </div>
            </div>

            <!-- Slug -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Slug
                </label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-900 font-mono text-sm">{{ role.slug }}</p>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                </label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 min-h-[80px]">
                    <p class="text-gray-900">{{ role.description || 'Sin descripción' }}</p>
                </div>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Estado
                </label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <span 
                        :class="[
                            'px-3 py-1 rounded-full text-sm font-semibold',
                            role.state 
                                ? 'bg-green-100 text-green-800' 
                                : 'bg-red-100 text-red-800'
                        ]"
                    >
                        {{ role.state ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Creación
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900 text-sm">
                            {{ role.created_at ? new Date(role.created_at).toLocaleString('es-ES') : 'N/A' }}
                        </p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Última Actualización
                    </label>
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-gray-900 text-sm">
                            {{ role.updated_at ? new Date(role.updated_at).toLocaleString('es-ES') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end">
                <button
                    @click="closeDialog"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors"
                >
                    Cerrar
                </button>
            </div>
        </template>
    </Dialog>
</template>
