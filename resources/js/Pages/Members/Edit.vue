<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    member: Object,
});

const form = useForm({
    _method: 'put',
    nama: props.member.nama,
    nis_nim: props.member.nis_nim,
    no_hp: props.member.no_hp,
    foto: null,
});

const pratinjau = ref(null);

// Status validasi NIS/NIM
const statusNis = ref('');
const pesanNis = ref('');

let timerNis = null;
let controllerNis = null;

// Validasi realtime NIS/NIM
watch(
    () => form.nis_nim,
    (nilai) => {
        clearTimeout(timerNis);

        if (controllerNis) {
            controllerNis.abort();
        }

        form.clearErrors('nis_nim');

        if (!nilai.trim()) {
            statusNis.value = '';
            pesanNis.value = '';
            return;
        }

        statusNis.value = 'checking';
        pesanNis.value = 'Memeriksa NIS/NIM...';

        timerNis = setTimeout(async () => {
            controllerNis = new AbortController();

            try {
                const response = await axios.get(
                    route('members.cek-nis'),
                    {
                        params: {
                            nis_nim: nilai.trim(),
                            abaikan: props.member.id,
                        },
                        signal: controllerNis.signal,
                    }
                );

                if (response.data.tersedia) {
                    statusNis.value = 'available';
                    pesanNis.value = 'NIS/NIM tersedia.';
                } else {
                    statusNis.value = 'used';
                    pesanNis.value = 'NIS/NIM sudah terdaftar.';
                }
            } catch (error) {
                if (error.name !== 'CanceledError') {
                    statusNis.value = 'error';
                    pesanNis.value = 'Gagal memeriksa NIS/NIM.';
                }
            }
        }, 300);
    },
    { immediate: true }
);

// Memilih foto baru
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

// Membersihkan timer dan pratinjau
onBeforeUnmount(() => {
    clearTimeout(timerNis);

    if (controllerNis) {
        controllerNis.abort();
    }

    if (pratinjau.value) {
        URL.revokeObjectURL(pratinjau.value);
    }
});

// Menyimpan perubahan anggota
function submit() {
    if (
        !form.nis_nim.trim() ||
        statusNis.value !== 'available'
    ) {
        return;
    }

    form.post(route('members.update', props.member.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Edit Anggota</h1>

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
                        v-if="pesanNis"
                        :class="{
                            'text-green-600': statusNis === 'available',
                            'text-red-600': statusNis === 'used'
                                || statusNis === 'error',
                            'text-gray-500': statusNis === 'checking'
                        }"
                    >
                        {{ pesanNis }}
                    </div>

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

                <!-- Foto Saat Ini -->
                <div>
                    <label class="block">Foto Saat Ini</label>

                    <img
                        v-if="member.foto"
                        :src="`/storage/${member.foto}`"
                        alt="Foto saat ini"
                        class="h-32 w-32 object-cover"
                    >

                    <span v-else>Belum ada foto</span>
                </div>

                <!-- Ganti Foto -->
                <div>
                    <label class="block">
                        Ganti Foto (kosongkan jika tidak ingin mengganti)
                    </label>

                    <input
                        type="file"
                        accept="image/png, image/jpeg"
                        @change="pilihFoto"
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
                        alt="Pratinjau foto baru"
                        class="mt-2 h-32 w-32 object-cover"
                    >
                </div>

                <!-- Tombol Simpan -->
                <button
                    type="submit"
                    :disabled="
                        form.processing ||
                        statusNis !== 'available' ||
                        !form.nis_nim.trim()
                    "
                    class="bg-blue-600 text-white px-4 py-2 rounded disabled:opacity-50"
                >
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>

                <Link
                    :href="route('members.index')"
                    class="ml-3"
                >
                    Kembali
                </Link>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
