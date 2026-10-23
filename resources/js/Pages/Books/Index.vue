<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    books: Object,
    search: {
        type: String,
        default: '',
    },
});

const page = usePage();
const keyword = ref(props.search ?? '');

// Menampilkan pesan berhasil dan pesan error
const successMessage = computed(() => page.props.flash?.success);
const errorMessage = computed(() => page.props.flash?.error);

// Fungsi pencarian buku
function cari() {
    router.get(
        '/books',
        { search: keyword.value },
        {
            preserveState: true,
            replace: true,
        }
    );
}

// Fungsi menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(`/books/${id}`);
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">Data Buku</h1>

            <!-- Pesan berhasil -->
            <div
                v-if="successMessage"
                class="mb-4 rounded bg-green-100 p-3 text-green-700"
            >
                {{ successMessage }}
            </div>

            <!-- Pesan error -->
            <div
                v-if="errorMessage"
                class="mb-4 rounded bg-red-100 p-3 text-red-700"
            >
                {{ errorMessage }}
            </div>

            <!-- Tombol tambah buku -->
            <div class="mb-4">
                <Link
                    href="/books/create"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    + Tambah Buku
                </Link>
            </div>

            <!-- Kotak pencarian -->
            <div class="mb-4">
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Cari kode, judul, atau pengarang buku..."
                    class="w-full rounded border p-2"
                    @keyup.enter="cari"
                />

                <button
                    type="button"
                    class="mt-2 rounded bg-gray-700 px-4 py-2 text-white"
                    @click="cari"
                >
                    Cari
                </button>
            </div>

            <!-- Tabel data buku -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border p-2">Kode</th>
                            <th class="border p-2">Judul</th>
                            <th class="border p-2">Pengarang</th>
                            <th class="border p-2">Penerbit</th>
                            <th class="border p-2">Tahun</th>
                            <th class="border p-2">Stok</th>
                            <th class="border p-2">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="book in props.books.data"
                            :key="book.id"
                        >
                            <td class="border p-2">
                                {{ book.kode_buku }}
                            </td>

                            <td class="border p-2">
                                {{ book.judul }}
                            </td>

                            <td class="border p-2">
                                {{ book.pengarang }}
                            </td>

                            <td class="border p-2">
                                {{ book.penerbit || '-' }}
                            </td>

                            <td class="border p-2">
                                {{ book.tahun || '-' }}
                            </td>

                            <td class="border p-2">
                                {{ book.stok }}
                            </td>

                            <td class="border p-2">
                                <div class="flex gap-2">
                                    <Link
                                        :href="`/books/${book.id}/edit`"
                                        class="rounded bg-yellow-500 px-3 py-1 text-white"
                                    >
                                        Edit
                                    </Link>

                                    <button
                                        type="button"
                                        class="rounded bg-red-600 px-3 py-1 text-white"
                                        @click="hapus(book.id)"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="props.books.data.length === 0">
                            <td
                                colspan="7"
                                class="border p-4 text-center"
                            >
                                Data buku tidak ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Navigasi halaman -->
            <div
                v-if="props.books.links"
                class="mt-4 flex flex-wrap gap-2"
            >
                <template
                    v-for="(link, index) in props.books.links"
                    :key="index"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="rounded border px-3 py-1"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                        }"
                        v-html="link.label"
                    />

                    <span
                        v-else
                        class="rounded border px-3 py-1 text-gray-400"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>