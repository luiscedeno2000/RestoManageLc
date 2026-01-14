<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';

/**
 * Props del componente
 */
defineProps({
    roles: {
        type: Array,
        default: () => [],
    },
});

/**
 * Emits del componente
 */
const emit = defineEmits(['view', 'edit', 'delete']);

/**
 * Manejar clic en botón Ver
 */
const handleView = (roleId) => {
    emit('view', roleId);
};

/**
 * Manejar clic en botón Editar
 */
const handleEdit = (roleId) => {
    emit('edit', roleId);
};

/**
 * Manejar clic en botón Eliminar
 */
const handleDelete = (roleId, roleName) => {
    emit('delete', roleId, roleName);
};
</script>

<template>
    <DataTable 
        :value="roles" 
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

        <!-- Columna: Slug -->
        <Column field="slug" header="Slug" :sortable="true">
            <template #body="slotProps">
                <span class="font-mono text-sm text-gray-600">{{ slotProps.data.slug }}</span>
            </template>
        </Column>

        <!-- Columna: Descripción -->
        <Column field="description" header="Descripción" :sortable="true">
            <template #body="slotProps">
                <span class="text-gray-700">{{ slotProps.data.description || 'Sin descripción' }}</span>
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
                        @click="handleView(slotProps.data.id)"
                    />

                    <!-- Botón Editar -->
                    <Button 
                        icon="pi pi-pencil" 
                        severity="warning"
                        size="small"
                        rounded
                        text
                        title="Editar"
                        @click="handleEdit(slotProps.data.id)"
                    />

                    <!-- Botón Eliminar -->
                    <Button 
                        icon="pi pi-trash" 
                        severity="danger"
                        size="small"
                        rounded
                        text
                        title="Eliminar"
                        @click="handleDelete(slotProps.data.id, slotProps.data.name)"
                    />
                </div>
            </template>
        </Column>
    </DataTable>
</template>
