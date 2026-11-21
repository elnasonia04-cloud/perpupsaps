<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Status validasi kode buku
const sedangMemeriksa = ref(false);
const kodeTersedia = ref(false);
const pesanKode = ref('');

let debounceTimer = null;
let controller = null;

// Validasi realtime kode buku
watch(
    () => form.kode_buku,
    (nilai) => {
        clearTimeout(debounceTimer);

        if (controller) {
            controller.abort();
        }

        kodeTersedia.value = false;
        pesanKode.value = '';

        const kode = nilai.trim();

        if (kode === '') {
            sedangMemeriksa.value = false;
            return;
        }

        sedangMemeriksa.value = true;

        debounceTimer = setTimeout(async () => {
            controller = new AbortController();

            try {
                const response = await axios.get('/books/cek-kode', {
                    params: {
                        kode_buku: kode,
                    },
                    signal: controller.signal,
                });

                if (response.data.tersedia) {
                    kodeTersedia.value = true;
                    pesanKode.value = 'Kode buku tersedia.';
                } else {
                    kodeTersedia.value = false;
                    pesanKode.value = 'Kode buku sudah digunakan.';
                }
            } catch (error) {
                if (
                    error.name !== 'CanceledError' &&
                    error.name !== 'AbortError'
                ) {
                    pesanKode.value = 'Gagal memeriksa kode buku.';
                    kodeTersedia.value = false;
                    console.error('Gagal memeriksa kode buku:', error);
                }
            } finally {
                sedangMemeriksa.value = false;
            }
        }, 300);
    }
);

function submit() {
    if (!kodeTersedia.value || sedangMemeriksa.value) {
        return;
    }

    form.post(route('books.store'));
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
            <h1 class="mb-4 text-xl font-bold">
                Tambah Buku
            </h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label>Kode Buku</label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <!-- Pesan validasi realtime -->
                    <div
                        v-if="sedangMemeriksa"
                        class="mt-1 text-sm text-gray-500"
                    >
                        Memeriksa kode buku...
                    </div>

                    <div
                        v-else-if="pesanKode"
                        class="mt-1 text-sm"
                        :class="kodeTersedia
                            ? 'text-green-600'
                            : 'text-red-600'"
                    >
                        {{ pesanKode }}
                    </div>

                    <div class="text-red-600">
                        {{ form.errors.kode_buku }}
                    </div>
                </div>

                <div class="mb-4">
                    <label>Judul Buku</label>

                    <input
                        v-model="form.judul"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <div class="text-red-600">
                        {{ form.errors.judul }}
                    </div>
                </div>

                <div class="mb-4">
                    <label>Pengarang</label>

                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <div class="text-red-600">
                        {{ form.errors.pengarang }}
                    </div>
                </div>

                <div class="mb-4">
                    <label>Penerbit</label>

                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <div class="text-red-600">
                        {{ form.errors.penerbit }}
                    </div>
                </div>

                <div class="mb-4">
                    <label>Tahun Terbit</label>

                    <input
                        v-model="form.tahun"
                        type="number"
                        class="block w-full rounded border p-2"
                    />

                    <div class="text-red-600">
                        {{ form.errors.tahun }}
                    </div>
                </div>

                <div class="mb-4">
                    <label>Stok Buku</label>

                    <input
                        v-model="form.stok"
                        type="number"
                        class="block w-full rounded border p-2"
                    />

                    <div class="text-red-600">
                        {{ form.errors.stok }}
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="
                        form.processing ||
                        sedangMemeriksa ||
                        !kodeTersedia
                    "
                    class="rounded bg-blue-600 px-4 py-2 text-white disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan'
                    }}
                </button>

                <Link
                    href="/books"
                    class="ml-2 rounded bg-gray-500 px-4 py-2 text-white"
                >
                    Kembali
                </Link>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
