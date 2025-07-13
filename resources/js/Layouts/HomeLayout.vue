<template>
    <div>
        <Nav>
        </Nav>

        <MainContent>
            <slot/>
        </MainContent>

        <Footer>
        </Footer>

    </div>
</template>

<script setup>
import MainContent from "@/Components/MainContent.vue";
import Nav from "@/Components/Nav.vue";
import Footer from "@/Components/Footer.vue";
</script>