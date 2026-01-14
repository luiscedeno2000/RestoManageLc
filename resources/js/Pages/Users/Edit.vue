<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import MultiSelect from 'primevue/multiselect';
import Card from 'primevue/card';

/**
 * Props recibidos desde el controlador
 */
const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    roles: {
        type: Array,
        default: () => [],
    },
    currentRoleIds: {
        type: Array,
        default: () => null,
    },
});

/**
 * Formulario para actualizar el usuario
 */
const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    password: '',
    password_confirmation: '',
    state: props.user.state ?? true,
    role_ids: props.currentRoleIds || [],
});

/**
 * Opciones para el MultiSelect de roles
 */
const roleOptions = props.roles.map(role => ({
    label: role.name,
    value: role.id,
}));

/**
 * Enviar el formulario
 */
const submit = () => {
    // Si no se proporciona contraseña, no enviarla
    const formData = { ...form.data() };
    if (!formData.password) {
        delete formData.password;
        delete formData.password_confirmation;
    }

    form.transform(() => formData).put(route('users.update', props.user.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirigir al índice después de actualizar
        },
    });
};
</script>

<template>
    <AppLayout title="Editar Usuario">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Usuario
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <Card>
                    <template #content>
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Nombre -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    id="name"
                                    v-model="form.name"
                                    :class="{ 'p-invalid': form.errors.name }"
                                    class="w-full"
                                    placeholder="Ej: Juan Pérez"
                                />
                                <small v-if="form.errors.name" class="p-error">
                                    {{ form.errors.name }}
                                </small>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <InputText
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    :class="{ 'p-invalid': form.errors.email }"
                                    class="w-full"
                                    placeholder="Ej: juan@example.com"
                                />
                                <small v-if="form.errors.email" class="p-error">
                                    {{ form.errors.email }}
                                </small>
                            </div>

                            <!-- Contraseña -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Contraseña
                                </label>
                                <Password
                                    id="password"
                                    v-model="form.password"
                                    :class="{ 'p-invalid': form.errors.password }"
                                    class="w-full"
                                    :feedback="false"
                                    toggleMask
                                    placeholder="Dejar vacío para mantener la contraseña actual"
                                />
                                <small v-if="form.errors.password" class="p-error">
                                    {{ form.errors.password }}
                                </small>
                                <small class="text-gray-500">
                                    Dejar vacío si no deseas cambiar la contraseña.
                                </small>
                            </div>

                            <!-- Confirmar Contraseña -->
                            <div v-if="form.password">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Contraseña
                                </label>
                                <Password
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :class="{ 'p-invalid': form.errors.password_confirmation }"
                                    class="w-full"
                                    :feedback="false"
                                    toggleMask
                                />
                                <small v-if="form.errors.password_confirmation" class="p-error">
                                    {{ form.errors.password_confirmation }}
                                </small>
                            </div>

                            <!-- Roles -->
                            <div>
                                <label for="role_ids" class="block text-sm font-medium text-gray-700 mb-2">
                                    Roles
                                </label>
                                <MultiSelect
                                    id="role_ids"
                                    v-model="form.role_ids"
                                    :options="roleOptions"
                                    optionLabel="label"
                                    optionValue="value"
                                    :class="{ 'p-invalid': form.errors.role_ids }"
                                    class="w-full"
                                    placeholder="Selecciona los roles para este usuario"
                                    :filter="true"
                                    :showClear="true"
                                >
                                    <template #value="slotProps">
                                        <div v-if="slotProps.value && slotProps.value.length > 0" class="flex flex-wrap gap-1">
                                            <span
                                                v-for="roleId in slotProps.value"
                                                :key="roleId"
                                                class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                                            >
                                                {{ roleOptions.find(r => r.value === roleId)?.label }}
                                            </span>
                                        </div>
                                        <span v-else class="text-gray-400">Sin roles asignados</span>
                                    </template>
                                </MultiSelect>
                                <small v-if="form.errors.role_ids" class="p-error">
                                    {{ form.errors.role_ids }}
                                </small>
                                <small v-if="form.errors['role_ids.*']" class="p-error">
                                    {{ form.errors['role_ids.*'] }}
                                </small>
                                <small class="text-gray-500">
                                    Selecciona uno o más roles para asignar al usuario. Puede quedar sin roles.
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
                                    Usuario activo
                                </label>
                            </div>

                            <!-- Botones -->
                            <div class="flex justify-end gap-3 pt-4">
                                <Button
                                    type="button"
                                    label="Cancelar"
                                    severity="secondary"
                                    @click="$inertia.visit(route('users.index'))"
                                />
                                <Button
                                    type="submit"
                                    label="Actualizar Usuario"
                                    icon="pi pi-check"
                                    :disabled="form.processing"
                                    :loading="form.processing"
                                />
                            </div>
                        </form>
                    </template>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
