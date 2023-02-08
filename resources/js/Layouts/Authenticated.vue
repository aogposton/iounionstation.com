<script setup>
import navBar from "../components/navBar.vue";
import sidebar from "../components/sidebar.vue";
</script>
<template>
    <div>
        <navBar></navBar>
        <div
            class="container-fluid"
            style="
                background-image: url('/images/user-bg.jpg');
                background-size: cover;
                padding: 40px 20px 20px 40px;
            "
        >
            <div class="row">
                <sidebar class="col-2"></sidebar>
                <div class="col-10 p-0">
                    <div>
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
