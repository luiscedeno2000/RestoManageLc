<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

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
const emit = defineEmits(['update:visible', 'success']);

/**
 * Estado del componente
 */
const loading = ref(false);
const error = ref(null);

/**
 * Formulario para actualizar el rol
 */
const form = useForm({
    name: '',
    slug: '',
    description: '',
    state: true,
});

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
        const response = await fetch(route('roles.edit', props.roleId), {
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
        const role = data.role;

        // Llenar el formulario con los datos del rol
        form.name = role.name || '';
        form.slug = role.slug || '';
        form.description = role.description || '';
        form.state = role.state ?? true;
    } catch (err) {
        error.value = err.message || 'Error al cargar la información del rol';
    } finally {
        loading.value = false;
    }
};

/**
 * Cerrar el diálogo
 */
const closeDialog = () => {
    emit('update:visible', false);
    form.reset();
    error.value = null;
};

/**
 * Generar slug automáticamente desde el nombre
 */
const generateSlug = () => {
    if (!form.slug) {
        form.slug = form.name
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
    }
};

/**
 * Enviar el formulario
 */
const submit = () => {
    form.put(route('roles.update', props.roleId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            closeDialog();
            emit('success');
        },
    });
};

/**
 * Observar cambios en visible y roleId
 */
watch([() => props.visible, () => props.roleId], ([newVisible, newRoleId]) => {
    if (newVisible && newRoleId) {
        loadRole();
    } else if (!newVisible) {
        form.reset();
        error.value = null;
    }
}, { immediate: true });
</script>

<template>
    <Dialog 
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        modal 
        header="Editar Rol"
        :style="{ width: '50rem' }"
        :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
    >
        <div v-if="loading" class="flex justify-center items-center py-8">
            <i class="pi pi-spin pi-spinner text-2xl text-gray-500"></i>
        </div>

        <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg mb-6">
            <p class="text-red-800">{{ error }}</p>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-6">
            <!-- Nombre -->
            <div>
                <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Rol <span class="text-red-500">*</span>
                </label>
                <InputText
                    id="edit-name"
                    v-model="form.name"
                    @input="generateSlug"
                    :class="{ 'p-invalid': form.errors.name }"
                    class="w-full"
                    placeholder="Ej: Administrador"
                />
                <small v-if="form.errors.name" class="p-error">
                    {{ form.errors.name }}
                </small>
            </div>

            <!-- Slug -->
            <div>
                <label for="edit-slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-red-500">*</span>
                </label>
                <InputText
                    id="edit-slug"
                    v-model="form.slug"
                    :class="{ 'p-invalid': form.errors.slug }"
                    class="w-full font-mono"
                    placeholder="Ej: administrador"
                />
                <small v-if="form.errors.slug" class="p-error">
                    {{ form.errors.slug }}
                </small>
                <small class="text-gray-500">
                    El slug se genera automáticamente desde el nombre. Solo letras minúsculas, números y guiones.
                </small>
            </div>

            <!-- Descripción -->
            <div>
                <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                </label>
                <Textarea
                    id="edit-description"
                    v-model="form.description"
                    :class="{ 'p-invalid': form.errors.description }"
                    class="w-full"
                    rows="4"
                    placeholder="Descripción del rol..."
                />
                <small v-if="form.errors.description" class="p-error">
                    {{ form.errors.description }}
                </small>
            </div>

            <!-- Estado -->
            <div class="flex items-center">
                <Checkbox
                    id="edit-state"
                    v-model="form.state"
                    :binary="true"
                    inputId="edit-state"
                />
                <label for="edit-state" class="ml-2 text-sm font-medium text-gray-700">
                    Rol activo
                </label>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 pt-4">
                <Button
                    type="button"
                    label="Cancelar"
                    severity="secondary"
                    @click="closeDialog"
                />
                <Button
                    type="submit"
                    label="Actualizar Rol"
                    icon="pi pi-check"
                    :disabled="form.processing"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>
