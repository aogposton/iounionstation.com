<script>
import Layout from "@/Layouts/AccountControlLayout.vue";
export default {
    layout: Layout,
};
</script>

<script setup>
import { ref, onMounted, computed, reactive } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";

const userChanged = ref([]);
const showPasswordReset = ref(false);
const toastMessage = ref("");
const toastHeading = ref("");
const passwordReset = reactive({
    confirmPassword: null,
    password: null,
});

defineProps({
    user: Object,
});

const user = computed(() => usePage().props.user);

let toastElList = null;
let toastList = null;

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});

function saveNewPassword() {
    const newUser = Object.assign({}, user);
    toastMessage.value = `Successfully updated Password`;
    toastHeading.value = "Password";
    toastList[0].show();
    showPasswordReset.value = false;
    passwordReset.password = null;
    passwordReset.confirmPassword = null;
    // router.post(`/admin/edit-user/${newUser.id}`, newUser, {
    //     onSuccess: () => {
    //         router.reload({ only: ["users"] });
    //         toastMessage.value = `Successfully updated ${newUser.name}`;
    //         toastList[0].show();
    //     },
    // });
}
</script>

<template>
    <div>
        <Head title="Admin users" />
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div
                        class="container"
                        style="height: 100vh; background-color: white"
                    >
                        <div class="row">
                            <div class="col"><h1>Billing Info</h1></div>
                        </div>
                        <hr />
                        <div class="row m-3">
                            <div class="col">Nothing here yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast">
                <div class="toast-header">
                    <strong class="me-auto">{{ toastHeading }}</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
