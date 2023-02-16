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
const loading = ref(false);
const showNewCargo = ref(false);

let toastElList = null;
let toastList = null;

const userCan = computed(() => usePage().props.auth.can);
const cargo = computed(() => usePage().props.cargo);
const cargoTypes = computed(() => usePage().props.cargoTypes);

const selectedCargo = reactive({
    name: null,
    cargo_type_id: 0,
    title: null,
    body: null,
});

function saveNewCargo() {
    axios.post(`/cargo`, selectedCargo).then((response) => {
        cancelNewCargo();
        router.reload({ only: ["cargo"], preserveState: true });
    });
}

function deleteCargo(cargo) {
    axios.delete(`/cargo/${cargo.id}`).then(() => {
        router.reload({ only: ["cargo"], preserveState: true });
    });
}

function editCargo(cargo) {
    resetNewCargo();
    Object.assign(selectedCargo, cargo);
    showNewCargo.value = true;
}

function updateCargo(cargo) {
    axios.put(`/cargo`, selectedCargo).then((response) => {
        cancelNewCargo();
        router.reload({ only: ["cargo"], preserveState: true });
    });

    showNewCargo.value = false;
    resetNewCargo();
}

function createNewCargo() {
    resetNewCargo();
    showNewCargo.value = true;
}

function cancelNewCargo() {
    resetNewCargo();
    showNewCargo.value = false;
}

function resetNewCargo() {
    Object.assign(selectedCargo, {
        name: null,
        cargo_type_id: 0,
        title: null,
        body: null,
    });
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
        <Head title="Edit Sources" />
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <button
                        class="btn btn-sm btn-primary w-100"
                        @click="createNewCargo()"
                    >
                        Create Cargo
                    </button>
                    <div class="container-fluid mt-5">
                        <h2>My Cargo:</h2>
                        <div class="row" v-for="box in cargo" :key="box.id">
                            <div class="col">
                                {{ box.name || box.title || "Anonymous Cargo" }}
                            </div>
                            <div class="col d-flex justify-content-around">
                                <a href="javascript:;" @click="deleteCargo(box)"
                                    >delete</a
                                >
                                <a href="javascript:;" @click="editCargo(box)"
                                    >edit</a
                                >
                            </div>
                            <hr />
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div
                        class="w-100 m-0"
                        v-if="showNewCargo"
                        :key="selectedCargo"
                    >
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="name" class="form-label"
                                            >Name (optional)</label
                                        >
                                        <input
                                            class="form-control"
                                            id="name"
                                            placeholder="Fan Shoutout"
                                            v-model="selectedCargo.name"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label
                                            for="cargoType"
                                            class="form-label"
                                            >Cargo Type</label
                                        >
                                        <select
                                            class="form-control"
                                            v-model="
                                                selectedCargo.cargo_type_id
                                            "
                                        >
                                            <option :value="0">
                                                Select Cargo Type
                                            </option>
                                            <option
                                                v-for="cargoType in cargoTypes"
                                                :key="cargoType.id"
                                                :value="cargoType.id"
                                            >
                                                {{ cargoType.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div
                                        class="mb-3"
                                        v-if="
                                            cargoTypes.find(
                                                (ct) =>
                                                    ct.id ==
                                                    selectedCargo.cargo_type_id
                                            )?.name != 'Simple Text'
                                        "
                                    >
                                        <label for="subject" class="form-label"
                                            >Title/Subject</label
                                        >
                                        <input
                                            class="form-control"
                                            id="subject"
                                            placeholder="To whom it may concern"
                                            v-model="selectedCargo.title"
                                        />
                                    </div>
                                    <div class="mb-3">
                                        <label for="body" class="form-label"
                                            >Body</label
                                        >
                                        <textarea
                                            class="form-control"
                                            id="body"
                                            rows="3"
                                            v-model="selectedCargo.body"
                                        ></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <button
                                            class="btn btn-primary me-1"
                                            @click="saveNewCargo"
                                            v-if="!selectedCargo.id"
                                        >
                                            Save Cargo
                                        </button>
                                        <button
                                            class="btn btn-primary me-1"
                                            @click="updateCargo"
                                            v-if="selectedCargo.id"
                                        >
                                            Update Cargo
                                        </button>
                                        <button
                                            class="btn btn-warning"
                                            @click="cancelNewCargo"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
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
<style>
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
}
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
}
</style>
