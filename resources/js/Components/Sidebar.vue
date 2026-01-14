<script setup>
import { Link } from '@inertiajs/vue3';
import { useSidebarMenu } from '@/composables/useSidebarMenu';

/**
 * Composable para gestionar el menú del Sidebar
 * Obtiene los items del menú según el contexto actual
 */
const { menuItems } = useSidebarMenu();
</script>

<template>
    <aside class="w-64 bg-white h-full border-r border-gray-200 flex flex-col">
        <!-- Header del Sidebar -->
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Menú</h2>
        </div>

        <!-- Items del Menú -->
        <nav class="flex-1 overflow-y-auto p-2">
            <ul class="space-y-1">
                <li v-for="(item, index) in menuItems" :key="index">
                    <Link
                        :href="route(item.route)"
                        :class="[
                            'flex items-center px-4 py-3 rounded-lg transition-colors duration-150',
                            item.active
                                ? 'bg-blue-50 text-blue-700 font-medium border-l-4 border-blue-700'
                                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
                        ]"
                    >
                        <i :class="[item.icon, 'mr-3']" />
                        <span>{{ item.label }}</span>
                    </Link>
                </li>
            </ul>
        </nav>
    </aside>
</template>