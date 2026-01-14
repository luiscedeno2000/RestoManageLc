<script setup>
import { ref, watch, inject } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import Textarea from 'primevue/textarea';
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
    roleName: {
        type: String,
        default: '',
    },
});

/**
 * Emits del componente
 */
const emit = defineEmits(['update:visible', 'success']);

/**
 * Página actual para acceder a props
 */
const page = usePage();

/**
 * Obtener Toast inyectado del componente padre
 */
const toast = inject('toast', null);

/**
 * Estado para la doble confirmación
 */
const showConfirmation = ref(false);

/**
 * Formulario para eliminar el rol
 */
const form = useForm({
    deletion_reason: '',
});

/**
 * Cerrar el diálogo
 */
const closeDialog = () => {
    emit('update:visible', false);
    form.reset();
    form.clearErrors();
    showConfirmation.value = false;
};

/**
 * Mostrar confirmación
 */
const handleConfirm = () => {
    if (!form.deletion_reason.trim()) {
        return;
    }
    showConfirmation.value = true;
};

/**
 * Cancelar confirmación
 */
const cancelConfirmation = () => {
    showConfirmation.value = false;
};

/**
 * Enviar el formulario de eliminación
 */
const submit = () => {
    form.delete(route('roles.destroy', props.roleId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showConfirmation.value = false;
            closeDialog();
            emit('success');
            
            // Mostrar mensaje de éxito si toast está disponible
            if (toast) {
                toast.add({
                    severity: 'success',
                    summary: 'Éxito',
                    detail: 'Rol eliminado exitosamente.',
                    life: 3000,
                });
            }
        },
        onError: (errors) => {
            // Obtener mensaje de error desde diferentes fuentes
            let errorMessage = errors.error 
                || (Array.isArray(errors.error) ? errors.error[0] : null)
                || errors.deletion_reason;
            
            // Si no hay error en errors, buscar en errorBags
            if (!errorMessage && page.props.errorBags?.default?.error) {
                const errorBagError = page.props.errorBags.default.error;
                errorMessage = Array.isArray(errorBagError) ? errorBagError[0] : errorBagError;
            }
            
            // Si aún no hay mensaje, usar uno por defecto
            if (!errorMessage) {
                errorMessage = 'Ocurrió un error al eliminar el rol.';
            }
            
            // Mostrar mensaje de error en Toast si está disponible
            if (toast) {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: errorMessage,
                    life: 5000,
                });
            }
            
            // Si hay error general, volver al formulario inicial para que el usuario pueda cerrar el diálogo
            if (errors.error || page.props.errorBags?.default?.error) {
                showConfirmation.value = false;
            }
        },
    });
};

/**
 * Observar cambios en visible
 */
watch(() => props.visible, (newVisible) => {
    if (!newVisible) {
        form.reset();
        form.clearErrors();
        showConfirmation.value = false;
    }
});

/**
 * Observar errores de la página para mostrar en el formulario
 */
watch(() => page.props.errorBags?.default?.error, (error) => {
    if (error && props.visible && !showConfirmation.value) {
        // Si hay error y el diálogo está visible, volver al formulario inicial
        showConfirmation.value = false;
        
        // Mostrar toast con el error si está disponible
        const errorMessage = Array.isArray(error) ? error[0] : error;
        if (errorMessage && toast) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: errorMessage,
                life: 5000,
            });
        }
    }
});
</script>

<template>
    <Dialog 
        :visible="visible"
        @update:visible="emit('update:visible', $event)"
        modal 
        :header="showConfirmation ? 'Confirmar Eliminación' : 'Eliminar Rol'"
        :style="{ width: '50rem' }"
        :breakpoints="{ '960px': '75vw', '641px': '90vw' }"
        :closable="!showConfirmation"
        :closeOnEscape="!showConfirmation"
    >
        <!-- Formulario de motivo -->
        <div v-if="!showConfirmation">
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-yellow-800 font-semibold">
                    ¿Está seguro que desea eliminar el rol <strong>"{{ roleName }}"</strong>?
                </p>
                <p class="text-yellow-700 text-sm mt-2">
                    Esta acción no se puede deshacer. Por favor, proporcione un motivo para la eliminación.
                </p>
            </div>

            <form @submit.prevent="handleConfirm" class="space-y-6">
                <!-- Motivo de eliminación -->
                <div>
                    <label for="deletion_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo de Eliminación <span class="text-red-500">*</span>
                    </label>
                    <Textarea
                        id="deletion_reason"
                        v-model="form.deletion_reason"
                        :class="{ 'p-invalid': form.errors.deletion_reason }"
                        class="w-full"
                        rows="4"
                        placeholder="Describa el motivo por el cual desea eliminar este rol..."
                    />
                    <small v-if="form.errors.deletion_reason" class="p-error">
                        {{ form.errors.deletion_reason }}
                    </small>
                    <!-- Mostrar error general si existe (desde form.errors o errorBags) -->
                    <div v-if="form.errors.error || (page.props.errorBags?.default?.error)" class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 text-sm font-semibold">
                            <i class="pi pi-exclamation-triangle mr-2"></i>
                            {{ 
                                form.errors.error 
                                    ? (Array.isArray(form.errors.error) ? form.errors.error[0] : form.errors.error)
                                    : (Array.isArray(page.props.errorBags?.default?.error) 
                                        ? page.props.errorBags.default.error[0] 
                                        : page.props.errorBags?.default?.error)
                            }}
                        </p>
                    </div>
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
                        label="Continuar"
                        icon="pi pi-arrow-right"
                        severity="danger"
                        :disabled="!form.deletion_reason.trim() || form.processing"
                    />
                </div>
            </form>
        </div>

        <!-- Confirmación final -->
        <div v-else>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-semibold text-lg mb-2">
                    ⚠️ Confirmación Final
                </p>
                <p class="text-red-700">
                    Está a punto de eliminar permanentemente el rol <strong>"{{ roleName }}"</strong>.
                </p>
                <p class="text-red-700 mt-2">
                    <strong>Motivo:</strong> {{ form.deletion_reason }}
                </p>
                <p class="text-red-700 font-semibold mt-3">
                    Esta acción no se puede deshacer. ¿Desea continuar?
                </p>
            </div>

            <!-- Botones de confirmación -->
            <div class="flex justify-end gap-3 pt-4">
                <Button
                    type="button"
                    label="Cancelar"
                    severity="secondary"
                    @click="cancelConfirmation"
                />
                <Button
                    type="button"
                    label="Confirmar Eliminación"
                    icon="pi pi-trash"
                    severity="danger"
                    :disabled="form.processing"
                    :loading="form.processing"
                    @click="submit"
                />
            </div>
        </div>
    </Dialog>
</template>
