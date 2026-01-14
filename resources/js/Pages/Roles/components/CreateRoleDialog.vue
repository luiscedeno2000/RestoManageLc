<script setup>
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
});

/**
 * Emits del componente
 */
const emit = defineEmits(['update:visible', 'success']);

/**
 * Formulario para crear un nuevo rol
 */
const form = useForm({
    name: '',
    slug: '',
    description: '',
    state: true,
});

/**
 * Cerrar el diálogo
 */
const closeDialog = () => {
    emit('update:visible', false);
    form.reset();
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
    form.post(route('roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            closeDialog();
            emit('success');
        },
    });
};
</script>

<template>
    <Dialog 
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        modal 
        header="Crear Nuevo Rol"
        :style="{ width: '50rem' }"
        :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
    >
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Rol <span class="text-red-500">*</span>
                </label>
                <InputText
                    id="name"
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
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-red-500">*</span>
                </label>
                <InputText
                    id="slug"
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
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                </label>
                <Textarea
                    id="description"
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
                    id="state"
                    v-model="form.state"
                    :binary="true"
                    inputId="state"
                />
                <label for="state" class="ml-2 text-sm font-medium text-gray-700">
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
                    label="Crear Rol"
                    icon="pi pi-check"
                    :disabled="form.processing"
                    :loading="form.processing"
                />
            </div>
        </form>
    </Dialog>
</template>
