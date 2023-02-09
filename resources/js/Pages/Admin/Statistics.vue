<script>
import Layout from "@/Layouts/Authenticated.vue";
export default {
    layout: Layout,
};
</script>

<script setup>
import { ref } from "vue";
import { Head } from "@inertiajs/vue3";
// import navBar from "../../components/navBar.vue";

defineProps({
    functionTimeTrackers: Object,
});

const page = ref("ViewFunctionTimers");

const showPage = (pageName) => {
    page.value = pageName;
    console.log(page);
};
</script>

<template>
    <div>
        <Head title="Job Finder" />
        <navBar></navBar>

        <div class="container-fluid">
            <div class="row">
                <div class="col p-0">
                    <table class="table bg-dark text-light">
                        <thead>
                            <td>Id</td>
                            <td>Function Name</td>
                            <td>Start Time</td>
                            <td>Stop Time</td>
                            <td>Elapsed Time</td>
                            <td>Notes</td>
                        </thead>
                        <tbody>
                            <tr
                                v-for="ftt in functionTimeTrackers"
                                :key="ftt.id"
                            >
                                <td>{{ ftt.id }}</td>
                                <td>{{ ftt.function }}</td>
                                <td>{{ ftt.start }}</td>
                                <td>{{ ftt.stop }}</td>
                                <td>
                                    {{
                                        ftt.stop == "0"
                                            ? Date.now() / 1000 - ftt.start
                                            : ftt.stop - ftt.start
                                    }}
                                    sec.
                                </td>
                                <td>{{ ftt.notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
