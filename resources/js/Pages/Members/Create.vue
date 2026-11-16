<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onBeforeUnmount } from 'vue';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null,
});

const pratinjau = ref(null);

// Memilih foto anggota
function pilihFoto(event) {
    const berkas = event.target.files[0];

    form.foto = berkas ?? null;

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }

    pratinjau.value = berkas
        ? URL.createObjectURL(berkas)
        : null;
}

// Menghapus pratinjau ketika halaman ditutup
onBeforeUnmount(() => {
    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }
});

// Menyimpan data anggota
function submit() {
    form.post(route('members.store'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            pratinjau.value = null;
        },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">
                Tambah Anggota
            </h1>

            <form
                @submit.prevent="submit"
                class="mt-3 space-y-3"
            >
                <!-- Nama -->
                <div>
                    <label class="block">Nama</label>

                    <input
                        v-model="form.nama"
                        type="text"
                        class="border rounded p-2 w-full"
                    >

                    <div
                        v-if="form.errors.nama"
                        class="text-red-600"
                    >
                        {{ form.errors.nama }}
                    </div>
                </div>

                <!-- NIS/NIM -->
                <div>
                    <label class="block">NIS/NIM</label>

                    <input
                        v-model="form.nis_nim"
                        type="text"
                        class="border rounded p-2 w-full"
                    >

                    <div
                        v-if="form.errors.nis_nim"
                        class="text-red-600"
                    >
                        {{ form.errors.nis_nim }}
                    </div>
                </div>

                <!-- Nomor HP -->
                <div>
                    <label class="block">No. HP</label>

                    <input
                        v-model="form.no_hp"
                        type="text"
                        class="border rounded p-2 w-full"
                    >

                    <div
                        v-if="form.errors.no_hp"
                        class="text-red-600"
                    >
                        {{ form.errors.no_hp }}
                    </div>
                </div>

                <!-- Foto -->
                <div>
                    <label class="block">
                        Foto (JPG/PNG, maksimal 2 MB)
                    </label>

                    <input
                        type="file"
                        accept=".jpg,.jpeg,.png"
                        @change="pilihFoto"
                        class="mt-1"
                    >

                    <div
                        v-if="form.errors.foto"
                        class="text-red-600"
                    >
                        {{ form.errors.foto }}
                    </div>

                    <img
                        v-if="pratinjau"
                        :src="pratinjau"
                        alt="Pratinjau foto"
                        class="mt-2 h-32 w-32 rounded object-cover"
                    >
                </div>

                <!-- Tombol Simpan -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
