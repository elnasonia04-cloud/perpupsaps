<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="mb-4 text-xl font-bold">Tambah Buku</h1>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label>Kode Buku</label>
                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.kode_buku }}</div>
                </div>

                <div class="mb-4">
                    <label>Judul Buku</label>
                    <input
                        v-model="form.judul"
                        type="text"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.judul }}</div>
                </div>

                <div class="mb-4">
                    <label>Pengarang</label>
                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.pengarang }}</div>
                </div>

                <div class="mb-4">
                    <label>Penerbit</label>
                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.penerbit }}</div>
                </div>

                <div class="mb-4">
                    <label>Tahun Terbit</label>
                    <input
                        v-model="form.tahun"
                        type="number"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.tahun }}</div>
                </div>

                <div class="mb-4">
                    <label>Stok Buku</label>
                    <input
                        v-model="form.stok"
                        type="number"
                        class="block w-full rounded border p-2"
                    />
                    <div class="text-red-600">{{ form.errors.stok }}</div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded bg-blue-600 px-4 py-2 text-white"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
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
