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
const DAYS = [
    { name: "Sunday" },
    { name: "Monday" },
    { name: "Tuesday" },
    { name: "Wednesday" },
    { name: "Thursday" },
    { name: "Friday" },
    { name: "Saturday" },
];

const trackModel = {
    source_id: null,
    cargo_id: null,
    destination_id: null,
    accumulate: 0,
    frequency_id: 1,
    process_at: null,
    track_type_id: 1,
    scheduled_day: null,
    scheduled_time: null,
    process_day: null,
    process_time: null,
    is_routine: 0,
};

const routineFrequencies = [
    frequencies.value.find((f) => f.name == "Every day"),
    frequencies.value.find((f) => f.name == "Every week"),
];
const newTrack = reactive(JSON.parse(JSON.stringify(trackModel)));

function addNewTrack() {
    if (newTrack.track_type_id == 1) {
        if (!newTrack.cargo_id) {
            toastHeading.value = `Error`;
            toastMessage.value = `Select some cargo`;
            toastList[0].show();
            return;
        }

        const dateTime = `${newTrack.process_day} ${newTrack.process_time} `;
        const scheduled_dateTime = `${new Date().getDay()} ${
            newTrack.scheduled_time
        } `;
        newTrack.process_at = new Date(dateTime).toString();
        newTrack.scheduled_time = new Date(scheduled_dateTime).toString();

        if (newTrack.process_at == "Invalid Date" && !newTrack.is_routine) {
            toastHeading.value = `Error`;
            toastMessage.value = `Invalid Date`;
            toastList[0].show();
            return;
        }
    }

    delete newTrack.process_day;
    delete newTrack.process_time;

    console.log(newTrack);

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
    Object.assign(newTrack, JSON.parse(JSON.stringify(trackModel)));
}

function resetType() {
    Object.assign(
        newTrack,
        JSON.parse(
            JSON.stringify({
                source_id: null,
                cargo_id: null,
                destination_id: null,
                accumulate: 0,
                frequency_id: 1,
                process_at: null,
                scheduled_day: null,
                scheduled_time: null,
                is_routine: 0,
                process_day: null,
                process_time: null,
            })
        )
    );
}

onMounted(() => {
    toastElList = [].slice.call(document.querySelectorAll(".toast"));
    toastList = toastElList.map(function (toastEl) {
        return new bootstrap.Toast(toastEl);
    });
});
</script>
<template>
    <Head title="Tracks" />
    <h2 class="title">
        Tracks

        <span v-if="trackLimit > -1">: {{ trackCount }}/{{ trackLimit }}</span>
        <span v-else>: {{ trackCount }} (no limit)</span>
    </h2>

    <div class="container">
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
            <div class="col col-md-3">
                <div class="thread">
                    Type:
                    <select
                        class="form-control"
                        v-model="newTrack.track_type_id"
                        @change="resetType()"
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
                            {{ cargo.name || cargo.title || cargo.body }}
                        </option>
                    </select>
                    <select
                        class="form-control my-3"
                        v-if="newTrack.track_type_id == '2'"
                        v-model="newTrack.source_id"
                        :disabled="sources.length == 0"
                    >
                        <option :value="null">Select Source</option>

                        <option v-for="source in sources" :value="source.id">
                            {{
                                source.name ? source.name : source.query_string
                            }}
                        </option>
                        <option selected v-if="sources.length == 0">
                            No sources available
                        </option>
                    </select>
                </div>
            </div>
            <div
                class="col col-lg-3"
                v-if="newTrack.track_type_id == 1 && newTrack.cargo_id"
            >
                <div class="thread">
                    Schedule:
                    <select class="form-control" v-model="newTrack.is_routine">
                        <option :value="0">Once</option>
                        <option :value="1">Routine</option>
                    </select>
                    <hr />
                    <div v-if="!newTrack.is_routine">
                        <span>On day:</span>
                        <input
                            id="startDate"
                            class="form-control"
                            type="date"
                            v-model="newTrack.process_day"
                        />
                        <span>at time:</span>
                        <input
                            id="startDate"
                            class="form-control"
                            type="time"
                            v-model="newTrack.process_time"
                        />
                    </div>
                    <div v-if="newTrack.is_routine">
                        <span>Frequency</span>
                        <select
                            class="form-control"
                            v-model="newTrack.frequency_id"
                        >
                            <option
                                v-for="frequency in routineFrequencies"
                                :key="frequency.id"
                                :value="frequency.id"
                            >
                                {{ frequency.name }}
                            </option>
                        </select>
                        <div
                            v-if="
                                newTrack.frequency_id ==
                                frequencies.find((f) => f.name == 'Every week')
                                    .id
                            "
                        >
                            <span>On day:</span>

                            <select
                                class="form-control"
                                v-model="newTrack.scheduled_day"
                            >
                                <option v-for="day in DAYS" :value="day.name">
                                    {{ day.name }}
                                </option>
                            </select>
                            <span>at time:</span>
                            <input
                                id="startDate"
                                class="form-control"
                                type="time"
                                v-model="newTrack.scheduled_time"
                            />
                        </div>
                        <div
                            v-if="
                                newTrack.frequency_id ==
                                frequencies.find((f) => f.name == 'Every day')
                                    .id
                            "
                        >
                            <span>at time:</span>
                            <input
                                id="startDate"
                                class="form-control"
                                type="time"
                                v-model="newTrack.scheduled_time"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="col col-lg-3"
                v-if="newTrack.track_type_id == 2 && newTrack.source_id"
            >
                <div class="thread">
                    Accumulate:
                    <select class="form-control" v-model="newTrack.accumulate">
                        <option :value="0">Solo</option>
                        <option :value="1">Accumulated</option>
                    </select>
                </div>
            </div>
            <div
                class="col col-lg-3"
                v-if="newTrack.track_type_id == 2 && newTrack.source_id"
            >
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
            <div
                class="col col-lg-3"
                v-if="newTrack.cargo_id || newTrack.source_id"
            >
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
                <button class="btn btn-success w-100 my-1" @click="addNewTrack">
                    Add new track
                </button>
            </div>
            <div class="col-12">
                <button class="btn btn-warning w-100" @click="cancelNewTrack()">
                    Cancel
                </button>
            </div>
        </div>
        <hr />

        <h3 class="title">Active Tracks</h3>
        <div class="row">
            <div class="col-12 col-lg-3 thread" v-for="track in tracks">
                <div class="container-fluid">
                    <div class="row mb-3" v-if="track.track_type_id == 2">
                        <div class="col">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col">
                                        <label> Get data from</label>
                                        <select class="form-control" disabled>
                                            <option selected>
                                                {{
                                                    track.source.name
                                                        ? track.source.name
                                                        : track.source
                                                              .query_string
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label> Send data to</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.destination.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label>Accumulated or Solo?</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{
                                                    track.accumulate
                                                        ? "Accumulated"
                                                        : "Solo"
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label>How often?</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.frequency.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3" v-if="track.track_type_id == 1">
                        <div class="col">
                            <div class="container-fluid">
                                <div class="row mb-3">
                                    <div class="col">
                                        <label> Deliver Cargo</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.cargo.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label> to destination</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.destination.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label> scheduled</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{
                                                    track.is_routine
                                                        ? track.frequency.name
                                                        : "Once"
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3" v-if="!track.is_routine">
                                    <div class="col">
                                        <label> On </label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{
                                                    new Date(
                                                        track.process_at
                                                    ).toString()
                                                }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3" v-if="track.is_routine">
                                    <div class="col-12">
                                        <label>When</label>
                                        {{ scheduled_day }}
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.scheduled_day }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <label>At</label>
                                        <select class="form-control" disabled>
                                            <option selected disabled>
                                                {{ track.scheduled_time }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col">
                            <button
                                class="btn btn-danger w-100"
                                @click="deleteTrack(track)"
                            >
                                Delete
                            </button>
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
</template>
<style>
.thread {
    border-radius: 10px;
    background-color: white;
    color: black;
}
.right-side {
    border-radius: 10px;
    background-color: white;
    color: black;
}
</style>
