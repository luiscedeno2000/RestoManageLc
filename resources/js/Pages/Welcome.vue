<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome" />
    
    <div class="min-h-screen bg-gray-100">
        <main>
            <h1 class="text-3xl font-bold underline">
                ¡Bienvenido a tu app con Laravel, Inertia y Vue!
            </h1>
            <Link href="/about" class="text-blue-500 hover:text-blue-700">
                Ir a About
            </Link>
        </main>
    </div>
</template>