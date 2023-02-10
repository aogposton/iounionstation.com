<template>
    <nav
        class="navbar navbar-expand-lg"
        style="border-bottom: 1px solid #e2e2e8"
    >
        <div class="container-fluid">
            <a class="navbar-brand" href="/routes">Tomato</a>
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul
                    class="navbar-nav w-100 d-flex justify-content-between me-auto mb-2 mb-lg-0"
                >
                    <li class="nav-item"></li>
                    <li class="nav-item dropdown">
                        <a
                            class="nav-link dropdown-toggle"
                            href="#"
                            role="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            {{ user.email }}
                        </a>
                        <ul class="dropdown-menu">
                            <li v-if="can['edit_users']">
                                <a class="dropdown-item" href="/admin/users"
                                    >Edit Users</a
                                >
                            </li>

                            <li v-if="can['see_stats']">
                                <a
                                    class="dropdown-item"
                                    href="/admin/statistics"
                                    >See Statistics</a
                                >
                            </li>

                            <li>
                                <a class="dropdown-item" href="/logout"
                                    >Log out</a
                                >
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>
<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const user = computed(() => usePage().props.auth.user);
const can = computed(() => usePage().props.auth.can);
</script>
