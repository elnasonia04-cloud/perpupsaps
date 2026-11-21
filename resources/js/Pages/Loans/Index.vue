<script setup>
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    loans: Object,
    flash: Object,
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Data Peminjaman
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Daftar transaksi peminjaman buku perpustakaan.
                    </p>
                </div>

                <Link
                    href="/loans/create"
                    class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    + Pinjam Buku
                </Link>
            </div>

            <div
                v-if="flash?.success"
                class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-sm text-green-700"
            >
                {{ flash.success }}
            </div>

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-3">No</th>
                            <th class="border px-4 py-3">Nama Anggota</th>
                            <th class="border px-4 py-3">Judul Buku</th>
                            <th class="border px-4 py-3">Tanggal Pinjam</th>
                            <th class="border px-4 py-3">Rencana Kembali</th>
                            <th class="border px-4 py-3">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(loan, index) in loans.data"
                            :key="loan.id"
                            class="hover:bg-gray-50"
                        >
                            <td class="border px-4 py-3">
                                {{ loans.from + index }}
                            </td>

                            <td class="border px-4 py-3">
                                {{ loan.member?.nama ?? '-' }}
                            </td>

                            <td class="border px-4 py-3">
                                {{ loan.book?.judul ?? '-' }}
                            </td>

                            <td class="border px-4 py-3">
                                {{ loan.tanggal_pinjam }}
                            </td>

                            <td class="border px-4 py-3">
                                {{ loan.tanggal_kembali_rencana }}
                            </td>

                            <td class="border px-4 py-3">
                                <span
                                    class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700"
                                    v-if="loan.status === 'dipinjam'"
                                >
                                    Dipinjam
                                </span>

                                <span
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                    v-else
                                >
                                    Dikembalikan
                                </span>
                            </td>
                        </tr>

                        <tr v-if="loans.data.length === 0">
                            <td
                                colspan="6"
                                class="border px-4 py-8 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <Link
                    v-for="(link, index) in loans.links"
                    :key="index"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="rounded border px-3 py-2 text-sm"
                    :class="{
                        'bg-blue-600 text-white': link.active,
                        'pointer-events-none opacity-50': !link.url
                    }"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
