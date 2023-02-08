<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const userCan = computed(() => usePage().props.auth.can);
</script>
<template>
    <div class="d-flex flex-column align-items-center" id="sidebar">
        <Link class="sidebar-link" href="/threads">Threads</Link>
        <Link
            class="sidebar-link"
            v-if="userCan['add_threads']"
            href="/threads/query"
            >Add Thread</Link
        >
        <span class="sidebar-link-disabled" v-else>Add Thread</span>
    </div>
</template>

<style scoped>
#sidebar {
    height: 100vh;
}
.sidebar-link {
    border: 1px solid #e2e2e8;
    border-radius: 10px;
    background-color: white;
    color: black;
    width: 100%;
    text-align: center;
    text-decoration: none;
    height: 40px;
    margin: 5px;
    vertical-align: middle;
    line-height: 40px;
}
.sidebar-link-disabled {
    border: 1px solid #b7b7c6;
    border-radius: 10px;
    background-color: #b7b7c6;
    color: black;
    width: 100%;
    text-align: center;
    text-decoration: none;
    height: 40px;
    margin: 5px;
    vertical-align: middle;
    line-height: 40px;
}
.sidebar-link:hover {
    background-color: #e2e2e8;
}
.sidebar-link-disabled:hover {
    background-color: #b7b7c6;
    cursor: not-allowed;
}
</style>
