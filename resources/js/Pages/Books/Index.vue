<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    books: Object,
    search: {
        type: String,
        default: '',
    },
});

const page = usePage();

const keyword = ref(props.search ?? '');
const hasilBuku = ref(props.books.data ?? []);
const sedangMencari = ref(false);
const sudahMencari = ref(false);

const successMessage = computed(() => page.props.flash?.success);
const errorMessage = computed(() => page.props.flash?.error);

let debounceTimer = null;
let controller = null;

// Fungsi AJAX Live Search Buku
async function cari() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(async () => {
        // Batalkan request sebelumnya jika masih berjalan
        if (controller) {
            controller.abort();
        }

        controller = new AbortController();

        sedangMencari.value = true;
        sudahMencari.value = true;

        try {
            const response = await axios.get('/books/search', {
                params: {
                    q: keyword.value,
                },
                signal: controller.signal,
            });

            hasilBuku.value = response.data;
        } catch (error) {
            if (
                error.name !== 'CanceledError' &&
                error.name !== 'AbortError'
            ) {
                console.error('Gagal mencari buku:', error);
            }
        } finally {
            sedangMencari.value = false;
        }
    }, 800); // Debounce 800 milidetik
}

// Fungsi menghapus buku
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(`/books/${id}`, {
            onSuccess: () => {
                hasilBuku.value = hasilBuku.value.filter(
                    (book) => book.id !== id
                );
            },
        });
    }
}

// Membersihkan timer dan request saat halaman ditutup
onBeforeUnmount(() => {
    clearTimeout(debounceTimer);

    if (controller) {
        controller.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="mb-4 text-2xl font-bold">
                Data Buku
            </h1>

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

            <!-- Kotak Live Search -->
            <div class="mb-4">
                <input
                    v-model="keyword"
                    type="text"
                    placeholder="Cari kode, judul, pengarang, atau penerbit buku..."
                    class="w-full rounded border p-2"
                    @input="cari"
                />

                <p
                    v-if="sedangMencari"
                    class="mt-2 text-sm text-gray-500"
                >
                    Sedang mencari buku...
                </p>
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
                            v-for="book in hasilBuku"
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

                        <!-- Pesan jika data tidak ditemukan -->
                        <tr v-if="hasilBuku.length === 0">
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

            <!-- Pagination sebelum Live Search -->
            <div
                v-if="!sudahMencari && props.books.links"
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
