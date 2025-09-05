<template>
    <div class="flex items-center space-x-2">
        <Button 
            :icon="userIcon" 
            :class="buttonClasses"
            :aria-label="ariaLabel"
            @click="handleProfileClick"
        />
        <span class="text-lg font-semibold">{{ userName }}</span>
    </div>
</template>

<script setup>
import Button from 'primevue/button';
import { computed } from 'vue';

/**
 * Props del componente UserProfile
 */
const props = defineProps({
    /**
     * Nombre del usuario a mostrar
     */
    userName: {
        type: String,
        default: 'Usuario'
    },
    /**
     * Icono a mostrar en el botón
     */
    userIcon: {
        type: String,
        default: 'pi pi-user'
    },
    /**
     * Tamaño del botón
     */
    size: {
        type: String,
        default: 'lg',
        validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    /**
     * Si el botón está deshabilitado
     */
    disabled: {
        type: Boolean,
        default: false
    }
});

/**
 * Emits del componente
 */
const emit = defineEmits(['profile-click']);

/**
 * Clases CSS del botón basadas en las props
 */
const buttonClasses = computed(() => {
    const baseClasses = 'p-button-rounded p-button-text text-gray-300 hover:text-white';
    const sizeClasses = {
        sm: 'p-button-sm',
        md: 'p-button-md', 
        lg: 'p-button-lg',
        xl: 'p-button-xl'
    };
    
    return `${baseClasses} ${sizeClasses[props.size]}`;
});

/**
 * Aria label para accesibilidad
 */
const ariaLabel = computed(() => `Perfil de ${props.userName}`);

/**
 * Maneja el click en el botón de perfil
 */
const handleProfileClick = () => {
    if (!props.disabled) {
        emit('profile-click', {
            userName: props.userName,
            timestamp: new Date().toISOString()
        });
    }
};
</script>
