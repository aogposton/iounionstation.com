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
const registrationSuccess = ref(false);
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
    confirmPassword: null,
});

function submit() {
    router.post("/registration", form, {
        onError: (error) => {
            toastMessage.value = `${error.message}`;
            toastList[0].show();
            console.log(error);
        },
        onSuccess: () => {
            registrationSuccess.value = true;
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
        <Head title="Registration" />
        <div class="container" v-if="!registrationSuccess">
            <div class="row">
                <div class="col">
                    <div class="container">
                        <form @submit.prevent="submit">
                            <div class="row g-2 m-2">
                                <div
                                    class="col justify-content-center align-items-center d-flex"
                                >
                                    <h1 style="color: white; font-size: 100px">
                                        Register
                                    </h1>
                                </div>
                            </div>
                            <div class="row g-2 m-2">
                                <div
                                    class="col-6 offset-3 justify-content-center align-items-center d-flex"
                                >
                                    <input
                                        class="form-control"
                                        type="text"
                                        placeholder="Email"
                                        autocomplete="email"
                                        v-model="form.email"
                                    />
                                </div>
                            </div>
                            <div class="row g-2 m-2">
                                <div
                                    class="col-6 offset-3 justify-content-center align-items-center d-flex"
                                >
                                    <input
                                        class="form-control form-control-lg"
                                        type="password"
                                        placeholder="Password"
                                        v-model="form.password"
                                    />
                                </div>
                            </div>
                            <div class="row g-2 m-2">
                                <div
                                    class="col-6 offset-3 justify-content-center align-items-center d-flex"
                                >
                                    <input
                                        class="form-control form-control-lg"
                                        type="password"
                                        placeholder="Confirm Password"
                                        v-model="form.confirmPassword"
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
                                        Register
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container" v-else>
            <div class="row">
                <div class="col">
                    <div class="container">
                        <div class="row g-2 m-2">
                            <div class="col">
                                <h1 style="color: white; font-size: 100px">
                                    Tomato
                                </h1>
                                <h3 style="color: white">
                                    Registration successful:
                                </h3>
                                <h6 style="color: white">
                                    Check email for verification.
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast">
                <div class="toast-header">
                    <strong class="me-auto">Restration failed</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
