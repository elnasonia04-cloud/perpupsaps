<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <aside
        class="w-64 min-h-screen flex-shrink-0 bg-indigo-900 p-4 text-white"
    >
        <h2 class="mb-6 text-xl font-bold">
            Perpustakaan Mini
        </h2>

        <nav class="space-y-3">
            <Link
                href="/dashboard"
                class="block rounded px-3 py-2 hover:bg-indigo-700"
            >
                Dashboard
            </Link>

            <Link
                href="/"
                class="block rounded px-3 py-2 hover:bg-indigo-700"
            >
                Beranda
            </Link>

            <Link
                href="/"
                class="block rounded px-3 py-2 hover:bg-indigo-700"
            >
                Data Buku
            </Link>

            <Link
                href="/"
                class="block rounded px-3 py-2 hover:bg-indigo-700"
            >
                Data Anggota
            </Link>
        </nav>
    </aside>
</template>
