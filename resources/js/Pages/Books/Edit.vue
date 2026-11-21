<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    book: Object,
});

const form = useForm({
    kode_buku: props.book.kode_buku ?? '',
    judul: props.book.judul ?? '',
    pengarang: props.book.pengarang ?? '',
    penerbit: props.book.penerbit ?? '',
    tahun: props.book.tahun ?? '',
    stok: props.book.stok ?? 0,
});

const kodeTersedia = ref(true);
const sedangMemeriksa = ref(false);
const pesanKode = ref('');

let timer = null;
let controller = null;

// Validasi realtime kode buku
watch(
    () => form.kode_buku,
    (kode) => {
        clearTimeout(timer);

        if (controller) {
            controller.abort();
        }

        if (!kode || !kode.trim()) {
            kodeTersedia.value = false;
            sedangMemeriksa.value = false;
            pesanKode.value = '';
            return;
        }

        sedangMemeriksa.value = true;
        kodeTersedia.value = false;
        pesanKode.value = '';

        timer = setTimeout(async () => {
            controller = new AbortController();

            try {
                const response = await axios.get('/books/cek-kode', {
                    params: {
                        kode_buku: kode.trim(),
                        abaikan: props.book.id,
                    },
                    signal: controller.signal,
                });

                kodeTersedia.value = response.data.tersedia;

                pesanKode.value = response.data.tersedia
                    ? 'Kode buku tersedia.'
                    : 'Kode buku sudah digunakan.';
            } catch (error) {
                if (
                    error.name !== 'CanceledError' &&
                    error.name !== 'AbortError'
                ) {
                    kodeTersedia.value = false;
                    pesanKode.value = 'Gagal memeriksa kode buku.';
                    console.error(error);
                }
            } finally {
                sedangMemeriksa.value = false;
            }
        }, 300);
    },
    { immediate: true }
);

// Membersihkan timer dan request
onBeforeUnmount(() => {
    clearTimeout(timer);

    if (controller) {
        controller.abort();
    }
});

// Fungsi menyimpan perubahan
function submit() {
    if (!kodeTersedia.value || sedangMemeriksa.value) {
        return;
    }

    form.put(`/books/${props.book.id}`);
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">

            <h1 class="mb-4 text-xl font-bold">
                Edit Buku
            </h1>

            <form
                @submit.prevent="submit"
                class="space-y-3"
            >

                <!-- Kode Buku -->
                <div>
                    <label class="mb-1 block font-medium">
                        Kode Buku
                    </label>

                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <p
                        v-if="sedangMemeriksa"
                        class="mt-1 text-sm text-gray-500"
                    >
                        Memeriksa kode buku...
                    </p>

                    <p
                        v-else-if="pesanKode"
                        class="mt-1 text-sm"
                        :class="kodeTersedia
                            ? 'text-green-600'
                            : 'text-red-600'"
                    >
                        {{ pesanKode }}
                    </p>

                    <p class="mt-1 text-sm text-red-600">
                        {{ form.errors.kode_buku }}
                    </p>
                </div>

                <!-- Judul -->
                <div>
                    <label class="mb-1 block font-medium">
                        Judul Buku
                    </label>

                    <input
                        v-model="form.judul"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <p class="text-sm text-red-600">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <!-- Pengarang -->
                <div>
                    <label class="mb-1 block font-medium">
                        Pengarang
                    </label>

                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <p class="text-sm text-red-600">
                        {{ form.errors.pengarang }}
                    </p>
                </div>

                <!-- Penerbit -->
                <div>
                    <label class="mb-1 block font-medium">
                        Penerbit
                    </label>

                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="block w-full rounded border p-2"
                    />

                    <p class="text-sm text-red-600">
                        {{ form.errors.penerbit }}
                    </p>
                </div>

                <!-- Tahun -->
                <div>
                    <label class="mb-1 block font-medium">
                        Tahun Terbit
                    </label>

                    <input
                        v-model="form.tahun"
                        type="number"
                        min="1000"
                        max="9999"
                        class="block w-full rounded border p-2"
                    />

                    <p class="text-sm text-red-600">
                        {{ form.errors.tahun }}
                    </p>
                </div>

                <!-- Stok -->
                <div>
                    <label class="mb-1 block font-medium">
                        Stok Buku
                    </label>

                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="block w-full rounded border p-2"
                    />

                    <p class="text-sm text-red-600">
                        {{ form.errors.stok }}
                    </p>
                </div>

                <!-- Tombol -->
                <div class="flex gap-2 pt-2">

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
                                : 'Simpan Perubahan'
                        }}
                    </button>

                    <Link
                        href="/books"
                        class="rounded bg-gray-500 px-4 py-2 text-white"
                    >
                        Batal
                    </Link>

                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
