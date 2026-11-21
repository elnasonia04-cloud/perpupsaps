<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <aside class="min-h-screen w-56 shrink-0 bg-gray-800 text-white">
        <div class="border-b border-gray-700 px-5 py-4">
            <h2 class="text-lg font-bold">
                Perpustakaan Mini
            </h2>
        </div>

        <nav class="space-y-1 px-3 py-4">
            <Link
                href="/dashboard"
                class="block rounded px-3 py-2 hover:bg-gray-700"
                :class="{ 'bg-gray-700': page.url === '/dashboard' }"
            >
                Dasbor
            </Link>

            <Link
                href="/"
                class="block rounded px-3 py-2 hover:bg-gray-700"
                :class="{ 'bg-gray-700': page.url === '/' }"
            >
                Beranda
            </Link>

            <Link
                href="/books"
                class="block rounded px-3 py-2 hover:bg-gray-700"
                :class="{ 'bg-gray-700': page.url.startsWith('/books') }"
            >
                Data Buku
            </Link>

            <Link
                href="/members"
                class="block rounded px-3 py-2 hover:bg-gray-700"
                :class="{ 'bg-gray-700': page.url.startsWith('/members') }"
            >
                Data Anggota
            </Link>

            <Link
                href="/loans"
                class="block rounded px-3 py-2 hover:bg-gray-700"
                :class="{ 'bg-gray-700': page.url.startsWith('/loans') }"
            >
                Peminjaman
            </Link>
        </nav>
    </aside>
</template>
