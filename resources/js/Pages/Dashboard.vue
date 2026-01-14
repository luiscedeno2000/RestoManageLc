<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Index from '@/Pages/Home/Index.vue';
import Nav from '@/Components/Nav.vue';
import Footer from '@/Components/Footer.vue';

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <Index />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
