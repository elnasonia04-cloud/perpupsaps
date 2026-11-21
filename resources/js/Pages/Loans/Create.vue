<script setup>
import { Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    members: Array,
    books: Array,
});

const form = useForm({
    member_id: '',
    book_id: '',
});

function submit() {
    form.post('/loans');
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    Form Peminjaman Buku
                </h1>

                <p class="mt-1 text-sm text-gray-600">
                    Silakan pilih anggota dan buku yang akan dipinjam.
                </p>
            </div>

            <div class="max-w-2xl rounded-lg bg-white p-6 shadow">
                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Pilih Anggota -->
                    <div>
                        <label class="mb-2 block font-semibold text-gray-700">
                            Nama Anggota
                        </label>

                        <select
                            v-model="form.member_id"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">
                                -- Pilih Anggota --
                            </option>

                            <option
                                v-for="member in members"
                                :key="member.id"
                                :value="member.id"
                            >
                                {{ member.nama }} - {{ member.nis_nim }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.member_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.member_id }}
                        </p>
                    </div>

                    <!-- Pilih Buku -->
                    <div>
                        <label class="mb-2 block font-semibold text-gray-700">
                            Judul Buku
                        </label>

                        <select
                            v-model="form.book_id"
                            class="w-full rounded border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">
                                -- Pilih Buku --
                            </option>

                            <option
                                v-for="book in books"
                                :key="book.id"
                                :value="book.id"
                            >
                                {{ book.judul }} - Stok: {{ book.stok }}
                            </option>
                        </select>

                        <p
                            v-if="form.errors.book_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.book_id }}
                        </p>
                    </div>

                    <!-- Tombol -->
                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Peminjaman' }}
                        </button>

                        <Link
                            href="/loans"
                            class="rounded bg-gray-500 px-5 py-2 font-semibold text-white hover:bg-gray-600"
                        >
                            Kembali
                        </Link>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
