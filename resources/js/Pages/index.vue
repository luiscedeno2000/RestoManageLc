<template>
    <HomeLayout>
        <h1>RESTO MANAGE LC</h1>
    </HomeLayout>

    <Button 
        label="Boton prueva dependencia" 
        @click="handleClick"
        severity="success"
        class="mt-4"
    />
    <Toast />
</template>

<script setup>
import HomeLayout from "@/Layouts/homeLayout.vue";
import Button from "primevue/button";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const handleClick = () => {
    toast.add({
        severity: "info",
        summary: "Hola",
        detail: "Mensaje de prueba",
        life: 3000
    });
}
</script>
