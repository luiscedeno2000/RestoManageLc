<template>
    <nav class="w-full bg-green-600 text-gray-300 px-6 py-4 shadow-lg">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <!-- Botón de perfil de usuario a la izquierda -->
            <UserProfile 
                :user-name="currentUser.name"
                :user-icon="currentUser.icon"
                :size="currentUser.buttonSize"
                @profile-click="handleProfileClick"
            />

            <!-- Menú de navegación a la derecha -->
            <div class="flex items-center space-x-4">
                <Button 
                    label="Home" 
                    class="p-button-text text-gray-300 hover:text-white hover:bg-green-700"
                    @click="navigateToHome"
                />
                <Button 
                    label="Menú" 
                    class="p-button-text text-gray-300 hover:text-white hover:bg-green-700"
                    @click="navigateToMenu"
                />
                <Button 
                    label="Promociones" 
                    class="p-button-text text-gray-300 hover:text-white hover:bg-green-700"
                    @click="navigateToPromotions"
                />
                <Button 
                    label="Contacto" 
                    class="p-button-text text-gray-300 hover:text-white hover:bg-green-700"
                    @click="navigateToContact"
                />
            </div>
        </div>
    </nav>
</template>

<script setup>
import Button from 'primevue/button';
import UserProfile from './UserProfile.vue';
import { router } from '@inertiajs/vue3';
import { reactive } from 'vue';

/**
 * Datos del usuario actual
 */
const currentUser = reactive({
    name: 'Usuario',
    icon: 'pi pi-user',
    buttonSize: 'lg'
});

/**
 * Navegar a la página principal
 */
const navigateToHome = () => {
    router.visit('/v1/restomanagelc');
};

/**
 * Navegar a la página de menú
 */
const navigateToMenu = () => {
    router.visit('/v1/menu');
};

/**
 * Navegar a la página de promociones
 */
const navigateToPromotions = () => {
    router.visit('/v1/promociones');
};

/**
 * Navegar a la página de contacto
 */
const navigateToContact = () => {
    router.visit('/v1/contacto');
};

/**
 * Maneja el click en el perfil de usuario
 * 
 * @param {Object} event - Evento del click
 * @param {string} event.userName - Nombre del usuario
 * @param {string} event.timestamp - Timestamp del click
 */
const handleProfileClick = (event) => {
    console.log('Perfil clickeado:', event);
    // Aquí puedes agregar lógica adicional como abrir un dropdown de perfil
    // o navegar a una página de configuración de usuario
    router.visit('/v1/profile');
};
</script>
