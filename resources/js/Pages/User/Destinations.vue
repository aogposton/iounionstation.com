<script>
import Layout from "@/Layouts/CentralControlLayout.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import { router, usePage, Head } from "@inertiajs/vue3";
import axios from "axios";

const toastMessage = ref("");
const toastHeading = ref("");

let toastElList = null;
let toastList = null;
const loading = ref(false);

const userCan = computed(() => usePage().props.auth.can);
const destinations = computed(() => usePage().props.destinations);
const destinationTypes = computed(() => usePage().props.destinationTypes);

const blankDestination = {
    destination_type_id: 0,
    name: null,
    credential: null,
    deletable: true,
};
const newDestination = reactive(Object.assign({}, blankDestination));

function verifyDestination() {
    loading.value = true;

    axios
        .post(`/destinations/verify`, newDestination)
        .then((response) => {
            loading.value = false;
            clearNewDestination();
            router.reload({ only: ["destinations"] });
        })
        .catch((error) => {
            loading.value = false;
            toastHeading.value = `Error`;
            toastMessage.value = `${error.response.data}`;
            console.log(error.response.data);
            toastList[0].show();
        });
}

// function addSource() {
//     axios.post(`/sources`, newSource).then((response) => {
//         router.reload({ only: ["sources"], preserveState: true });
//     });
// }

function deleteDestination(destination) {
    axios.delete(`/destinations/${destination.id}`).then(() => {
        clearNewDestination();
        router.reload({ only: ["destinations"], preserveState: true });
    });
}

function clearNewDestination() {
    Object.assign(newDestination, blankDestination);
}
onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});
</script>
<template>
    <div>
        <Head title="Destinations" />
        <h2 class="title">Destinations</h2>
        <div class="container-fluid">
            <div class="row">
                <div
                    class="col-3 track"
                    v-for="destination in destinations"
                    :key="destination.id"
                >
                    <input
                        type="text"
                        class="form-control my-2 w-100"
                        placeholder="Name"
                        v-model="destination.name"
                        disabled
                    />
                    <select
                        class="form-control"
                        v-model="destination.destination_type_id"
                        disabled
                    >
                        <option
                            v-for="dt in destinationTypes"
                            :key="dt.id"
                            :value="dt.id"
                        >
                            {{ dt.name }}
                        </option>
                    </select>
                    <input
                        type="text"
                        class="form-control my-2 w-100"
                        placeholder="Search for Something"
                        v-model="destination.credential"
                        disabled
                    />
                    <button
                        @click="deleteDestination(destination)"
                        class="btn btn-danger w-100"
                        type="submit"
                        :disabled="!destination.deletable"
                        v-if="destination.verified_at"
                    >
                        Delete
                    </button>
                    <span v-if="!destination.verified_at">
                        Awaiting Verification
                    </span>
                    <button
                        @click="deleteDestination(destination)"
                        class="btn btn-danger w-100"
                        type="submit"
                        :disabled="!destination.deletable"
                        v-if="!destination.verified_at"
                    >
                        Delete
                    </button>
                </div>

                <div class="col-3 track">
                    Add destination
                    <input
                        type="text"
                        class="form-control my-2 w-100"
                        placeholder="Name"
                        v-model="newDestination.name"
                    />
                    <select
                        class="form-control"
                        v-model="newDestination.destination_type_id"
                    >
                        <option value="0">Select a type</option>
                        <option
                            v-for="dt in destinationTypes"
                            :key="dt.id"
                            :value="dt.id"
                        >
                            {{ dt.name }}
                        </option>
                    </select>
                    <input
                        type="text"
                        class="form-control my-2 w-100"
                        placeholder="Search for Something"
                        v-model="newDestination.credential"
                    />

                    <button
                        @click="verifyDestination()"
                        class="btn btn-danger w-100"
                        type="submit"
                    >
                        <span v-if="loading">Loading</span>
                        <span v-else>Verify Destination</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="toast-container bottom-0 end-0 p-3">
            <div class="toast bg-danger">
                <div class="toast-header">
                    <strong class="me-auto">{{ toastHeading }}</strong>
                </div>
                <div class="toast-body">{{ toastMessage }}</div>
            </div>
        </div>
    </div>
</template>
<style>
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
.track {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
