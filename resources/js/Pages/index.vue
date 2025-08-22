<template>
    <HomeLayout>
        <MainContent>
            <h1>RESTO MANAGE LC</h1>
        </MainContent>
    </HomeLayout>

    <Button 
        @click="handleClick"
        label="Boton prueva dependencia" 
        severity="success"
        class="mt-4"
    />
    <Toast />

</template>

<script setup>
import HomeLayout from "@/Layouts/HomeLayout.vue";
import Button from "primevue/button";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";
import MainContent from "@/Components/MainContent.vue";

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
