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
let toastElList = null;
let toastList = null;

const sources = computed(() => usePage().props.sources);
const destinations = computed(() => usePage().props.destinations);
const sourceTypes = computed(() => usePage().props.sourceTypes);
const tracks = computed(() => usePage().props.tracks);
const frequencies = computed(() => usePage().props.frequencies);
const trackTypes = computed(() => usePage().props.trackTypes);
const cargo = computed(() => usePage().props.cargo);
const trackLimit = computed(() => usePage().props.auth.track_limit);
const trackCount = computed(() => usePage().props.auth.track_count);

const showCreateTrack = ref(false);

const trackModel = {
    source_id: null,
    cargo_id: null,
    destination_id: null,
    accumulate: 0,
    frequency_id: 1,
    process_at: null,
    track_type_id: 1,
};

const newTrack = reactive(trackModel);

function addNewTrack() {
    axios.post(`/tracks`, newTrack).then((response) => {
        cancelNewTrack();
        router.reload({
            only: ["tracks", "track_count", "track_limit"],
            preserveState: true,
        });
    });
}

function deleteTrack(track) {
    if (confirm("delete?")) {
        axios.delete(`/tracks/${track.id}`, newTrack).then((response) => {
            router.reload({
                only: ["tracks", "track_count", "track_limit"],
                preserveState: true,
            });
        });
    }
}

function createNewTrack() {
    resetNewTrack();
    showCreateTrack.value = true;
}

function cancelNewTrack() {
    resetNewTrack();
    showCreateTrack.value = false;
}

function resetNewTrack() {
    Object.assign(newTrack, trackModel);
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
        <Head title="Tracks" />
        <h2 class="title">
            Tracks

            <span v-if="trackLimit > -1"
                >: {{ trackCount }}/{{ trackLimit }}</span
            >
            <span v-else>: {{ trackCount }} (no limit)</span>
        </h2>
        <div class="container-fluid">
            <div class="row thread" v-if="!showCreateTrack">
                <div class="col-12">
                    <button
                        class="btn btn-success w-100"
                        @click="showCreateTrack = true"
                    >
                        Create new track
                    </button>
                </div>
            </div>
            <div class="row thread" v-if="showCreateTrack">
                <div class="col-md-3">
                    <div class="thread">
                        Type:
                        <select
                            class="form-control"
                            v-model="newTrack.track_type_id"
                        >
                            <option
                                v-for="trackType in trackTypes"
                                :key="trackType.id"
                                :value="trackType.id"
                            >
                                {{ trackType.name }}
                            </option>
                        </select>
                        <select
                            class="form-control my-3"
                            v-if="newTrack.track_type_id == '1'"
                            v-model="newTrack.cargo_id"
                            :disabled="cargo.length == 0"
                        >
                            <option selected v-if="cargo.length == 0">
                                No cargo available
                            </option>

                            <option :value="null">Select Cargo</option>

                            <option
                                v-for="cargo in cargo"
                                :key="cargo.id"
                                :value="cargo.id"
                            >
                                {{ cargo.name }}
                            </option>
                        </select>
                        <select
                            class="form-control my-3"
                            v-if="newTrack.track_type_id == '2'"
                            v-model="newTrack.source_id"
                            :disabled="sources.length == 0"
                        >
                            <option :value="null">Select Source</option>

                            <option
                                v-for="source in sources"
                                :value="source.id"
                            >
                                {{ source.name }}
                            </option>
                            <option selected v-if="sources.length == 0">
                                No sources available
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-3" v-if="newTrack.track_type_id == 2">
                    <div class="thread">
                        Accumulate:
                        <select
                            class="form-control"
                            v-model="newTrack.accumulate"
                        >
                            <option :value="0">Per post</option>
                            <option :value="1">Multiple posts into one</option>
                        </select>
                    </div>
                </div>
                <div class="col-3" v-if="newTrack.track_type_id == 2">
                    <div class="thread">
                        frequencies:
                        <select
                            class="form-control"
                            v-model="newTrack.frequency_id"
                        >
                            <option
                                v-for="frequency in frequencies"
                                :key="frequency.id"
                                :value="frequency.id"
                            >
                                {{ frequency.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="thread">
                        destination:

                        <select
                            class="form-control"
                            v-model="newTrack.destination_id"
                        >
                            <option
                                v-for="destination in destinations"
                                :key="destination.id"
                                :value="destination.id"
                            >
                                {{ destination.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12">
                    <button
                        class="btn btn-success w-100 my-1"
                        @click="addNewTrack"
                    >
                        Add new track
                    </button>
                </div>
                <div class="col-12">
                    <button
                        class="btn btn-warning w-100"
                        @click="cancelNewTrack()"
                    >
                        Cancel
                    </button>
                </div>
            </div>
            <div class="row thread" v-for="track in tracks">
                {{ track }}
                <div class="col-12">
                    <button
                        class="btn btn-danger w-100"
                        @click="deleteTrack(track)"
                    >
                        Delete
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
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
    margin: 5px 10px;
    padding: 10px;
}
</style>
