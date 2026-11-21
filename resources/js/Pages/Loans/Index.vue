<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    loans: Object,
    flash: Object,
});

const page = usePage();

// Memproses pengembalian buku
function kembalikanBuku(loan) {
    const denda = loan.perkiraan_denda ?? 0;

    const pesan =
        `Apakah kamu yakin ingin mengembalikan buku "${loan.book?.judul ?? '-'}"?\n\n` +
        `Perkiraan denda: Rp${Number(denda).toLocaleString('id-ID')}`;

    if (!confirm(pesan)) {
        return;
    }

    router.patch(
        `/loans/${loan.id}/kembalikan`,
        {},
        {
            preserveScroll: true,
        }
    );
}
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

            <!-- Pesan berhasil -->
            <div
                v-if="flash?.success"
                class="mb-4 rounded border border-green-300 bg-green-100 p-3 text-sm text-green-700"
            >
                {{ flash.success }}
            </div>

            <!-- Pesan kesalahan -->
            <div
                v-if="page.props.errors?.loan"
                class="mb-4 rounded border border-red-300 bg-red-100 p-3 text-sm text-red-700"
            >
                {{ page.props.errors.loan }}
            </div>

            <div class="overflow-x-auto rounded-lg bg-white shadow">
                <table class="w-full border-collapse text-left text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="border px-4 py-3">No</th>
                            <th class="border px-4 py-3">Nama Anggota</th>
                            <th class="border px-4 py-3">Kode Buku</th>
                            <th class="border px-4 py-3">Judul Buku</th>
                            <th class="border px-4 py-3">Tanggal Pinjam</th>
                            <th class="border px-4 py-3">Rencana Kembali</th>
                            <th class="border px-4 py-3">Tanggal Kembali</th>
                            <th class="border px-4 py-3">Denda</th>
                            <th class="border px-4 py-3">Status</th>
                            <th class="border px-4 py-3">Aksi</th>
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
                                {{ loan.book?.kode_buku ?? '-' }}
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
                                {{ loan.tanggal_kembali ?? '-' }}
                            </td>

                            <td class="border px-4 py-3">
                                Rp{{ Number(loan.denda ?? 0).toLocaleString('id-ID') }}
                            </td>

                            <td class="border px-4 py-3">
                                <span
                                    v-if="loan.status === 'dipinjam'"
                                    class="rounded-full bg-yellow-100 px-3 py-1 text-xs font-semibold text-yellow-700"
                                >
                                    Dipinjam
                                </span>

                                <span
                                    v-else
                                    class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700"
                                >
                                    Dikembalikan
                                </span>
                            </td>

                            <td class="border px-4 py-3">
                                <button
                                    v-if="loan.status === 'dipinjam'"
                                    type="button"
                                    @click="kembalikanBuku(loan)"
                                    class="rounded bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700"
                                >
                                    Kembalikan
                                </button>

                                <span
                                    v-else
                                    class="text-xs text-gray-500"
                                >
                                    Selesai
                                </span>
                            </td>
                        </tr>

                        <tr v-if="loans.data.length === 0">
                            <td
                                colspan="10"
                                class="border px-4 py-8 text-center text-gray-500"
                            >
                                Belum ada data peminjaman.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
