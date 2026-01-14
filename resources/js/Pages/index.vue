<template>
    <div>
        <MainContent>
            <h1>RESTO MANAGE LC</h1>
        </MainContent>

        <Button 
            @click="handleClick"
            label="Boton prueva dependencia" 
            severity="success"
            class="mt-4"
        />
        
        <!-- Toast debe estar en el template para que useToast funcione -->
        <Toast />
    </div>
</template>

<script setup>
import { provide } from 'vue';
import Button from "primevue/button";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import MainContent from "@/Components/MainContent.vue";

/**
 * Configurar Toast - ahora que ToastService está registrado globalmente
 */
const toast = useToast();
provide('toast', toast);

const handleClick = () => {
    toast.add({
        severity: "info",
        summary: "Hola",
        detail: "Mensaje de prueba",
        life: 3000
    });
}
</script>
