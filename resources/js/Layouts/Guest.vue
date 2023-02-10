<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">IO Union Station</a>
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
            <div
                class="collapse navbar-collapse d-flex"
                id="navbarSupportedContent"
            >
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item mx-2">
                        <a href="/login">Login</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a href="/register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section>
        <slot />
    </section>
</template>
<style>
.collapse {
    visibility: visible;
}
</style>
