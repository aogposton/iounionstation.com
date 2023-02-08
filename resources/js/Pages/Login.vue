<script>
import Layout from "@/Layouts/Guest.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { Head, router } from "@inertiajs/vue3";
import { reactive, ref, onMounted } from "vue";

const toastMessage = ref("");
let toastElList = null;
let toastList = null;
onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

const form = reactive({
    email: null,
    password: null,
});

function submit() {
    router.post("/login", form, {
        onError: (errors) => {
            toastMessage.value = `${errors.email}`;
            toastList[0].show();
            console.log(errors);
        },
    });
}
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
        <Head title="Login" />
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="container">
                        <form @submit.prevent="submit">
                            <div class="row g-2 m-2">
                                <div
                                    class="col justify-content-center align-items-center d-flex"
                                >
                                    <h1
                                        class="text-7xl mb-2"
                                        style="color: white"
                                    >
                                        Login
                                    </h1>
                                </div>
                            </div>
                            <div class="row g-2 m-2">
                                <div
                                    class="col justify-content-center align-items-center d-flex"
                                >
                                    <input
                                        type="text"
                                        placeholder="Email"
                                        autocomplete="username"
                                        v-model="form.email"
                                    />
                                </div>
                            </div>
                            <div class="row g-2 m-2">
                                <div
                                    class="col justify-content-center align-items-center d-flex"
                                >
                                    <input
                                        class="form-control form-control-lg"
                                        type="password"
                                        placeholder="Password"
                                        v-model="form.password"
                                        autocomplete="current-password"
                                    />
                                </div>
                            </div>
                            <div class="row g-2">
                                <div
                                    class="col justify-content-center align-items-center d-flex"
                                >
                                    <button
                                        type="submit"
                                        class="btn btn-primary"
                                    >
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast">
                <div class="toast-header">
                    <strong class="me-auto">Login failed</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
