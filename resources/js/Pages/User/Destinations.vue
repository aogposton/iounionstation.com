<script>
import Layout from "@/Layouts/CentralControlLayout.vue";
export default {
    layout: Layout,
};
</script>
<script setup>
import { reactive, onMounted, ref, computed } from "vue";
import { router, usePage, Head } from "@inertiajs/vue3";

const toastMessage = ref("");
let toastElList = null;
let toastList = null;

const userCan = computed(() => usePage().props.auth.can);
const destinations = computed(() => usePage().props.destinations);
const destinationTypes = computed(() => usePage().props.destinationTypes);

const newDestination = reactive({
    destination_type_id: 0,
    name: null,
    credential: null,
    deletable: true,
});
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
                        :disabled="!destination.deletable"
                    />
                    <select
                        class="form-control"
                        v-model="destination.destination_type_id"
                        :disabled="!destination.deletable"
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
                        :disabled="!destination.deletable"
                    />
                    <button
                        @click="submitForm()"
                        class="btn btn-danger w-100"
                        type="submit"
                        :disabled="!destination.deletable"
                    >
                        Delete
                    </button>
                </div>

                <div class="col-3 track">
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
                        @click="submitForm()"
                        class="btn btn-danger w-100"
                        type="submit"
                    >
                        Add Destination
                    </button>
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
<style>
.title {
    color: white;
}
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
