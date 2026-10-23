<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

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

function submit() {
    form.put(`/books/${props.book.id}`);
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="mb-6 text-2xl font-bold">Edit Buku</h1>

            <form
                @submit.prevent="submit"
                class="max-w-xl space-y-4"
            >
                <div>
                    <label class="mb-1 block font-medium">Kode Buku</label>
                    <input
                        v-model="form.kode_buku"
                        type="text"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.kode_buku" class="mt-1 text-sm text-red-600">
                        {{ form.errors.kode_buku }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Judul Buku</label>
                    <input
                        v-model="form.judul"
                        type="text"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.judul" class="mt-1 text-sm text-red-600">
                        {{ form.errors.judul }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Pengarang</label>
                    <input
                        v-model="form.pengarang"
                        type="text"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.pengarang" class="mt-1 text-sm text-red-600">
                        {{ form.errors.pengarang }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Penerbit</label>
                    <input
                        v-model="form.penerbit"
                        type="text"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.penerbit" class="mt-1 text-sm text-red-600">
                        {{ form.errors.penerbit }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Tahun Terbit</label>
                    <input
                        v-model="form.tahun"
                        type="number"
                        min="1000"
                        max="9999"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.tahun" class="mt-1 text-sm text-red-600">
                        {{ form.errors.tahun }}
                    </p>
                </div>

                <div>
                    <label class="mb-1 block font-medium">Stok</label>
                    <input
                        v-model="form.stok"
                        type="number"
                        min="0"
                        class="w-full rounded border p-2"
                    />
                    <p v-if="form.errors.stok" class="mt-1 text-sm text-red-600">
                        {{ form.errors.stok }}
                    </p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50"
                    >
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
