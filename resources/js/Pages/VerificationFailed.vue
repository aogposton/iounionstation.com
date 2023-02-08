<script>
import Layout from "@/Layouts/Guest.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({
    error: Object,
});
</script>

<template>
    <div
        class="d-flex align-items-center"
        style="
            height: 100vh;
            background-image: url('/images/login-bg.jpg');
            background-size: cover;
        "
    >
        <Head title="registration" />
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="container">
                        <div class="row g-2 m-2">
                            <div class="col">
                                <h1 style="color: white; font-size: 100px">
                                    Tomato
                                </h1>
                                <h3 style="color: white">
                                    Verification failed.
                                </h3>
                                <h6 style="color: white">
                                    {{ error }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
