<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    statistik: {
        type: Object,
        default: () => ({
            total_buku: 0,
            total_stok: 0,
            total_anggota: 0,
            sedang_dipinjam: 0,
            peminjaman_hari_ini: 0,
            terlambat: 0,
            total_denda: 0,
        }),
    },
    grafik: {
        type: Object,
        default: () => ({
            label: [],
            jumlah: [],
        }),
    },
});

const formatAngka = (angka) => {
    return new Intl.NumberFormat('id-ID').format(angka ?? 0);
};
</script>

<template>
    <Head title="Dashboard Perpustakaan Mini" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Perpustakaan Mini
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

                <!-- Kartu Statistik -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">

                    <!-- Total Buku -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Total Buku
                        </p>
                        <p class="mt-2 text-3xl font-bold text-blue-600">
                            {{ formatAngka(statistik.total_buku) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Jumlah judul buku
                        </p>
                    </div>

                    <!-- Total Stok -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Total Stok Buku
                        </p>
                        <p class="mt-2 text-3xl font-bold text-green-600">
                            {{ formatAngka(statistik.total_stok) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Jumlah seluruh stok
                        </p>
                    </div>

                    <!-- Total Anggota -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Total Anggota
                        </p>
                        <p class="mt-2 text-3xl font-bold text-purple-600">
                            {{ formatAngka(statistik.total_anggota) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Anggota perpustakaan
                        </p>
                    </div>

                    <!-- Peminjaman Aktif -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Sedang Dipinjam
                        </p>
                        <p class="mt-2 text-3xl font-bold text-orange-600">
                            {{ formatAngka(statistik.sedang_dipinjam) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Peminjaman belum dikembalikan
                        </p>
                    </div>

                    <!-- Peminjaman Hari Ini -->
                    <div class="overflow-hidden rounded-lg bg-white p-6 shadow-sm">
                        <p class="text-sm font-medium text-gray-500">
                            Peminjaman Hari Ini
                        </p>
                        <p class="mt-2 text-3xl font-bold text-indigo-600">
                            {{ formatAngka(statistik.peminjaman_hari_ini) }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Transaksi pada hari ini
                        </p>
                    </div>

                </div>

                <!-- Grafik Peminjaman 12 Bulan -->
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800">
                            Grafik Peminjaman 12 Bulan Terakhir
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Statistik peminjaman selama 12 bulan terakhir
                        </p>
                    </div>

                    <div class="p-6">
                        <BarChart
                            :labels="grafik.label"
                            :data="grafik.jumlah"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
