<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    members: Object,
    search: String,
});

const keyword = ref(props.search ?? '');
const daftarAnggota = ref(props.members.data ?? []);
const sedangMencari = ref(false);
const sudahMencari = ref(false);
const pesanError = ref('');

let timer = null;
let controller = null;

function cari() {
    clearTimeout(timer);

    // Batalkan request pencarian sebelumnya
    if (controller) {
        controller.abort();
    }

    pesanError.value = '';

    // Jika kolom pencarian kosong, tampilkan data awal
    if (keyword.value.trim() === '') {
        sudahMencari.value = false;
        sedangMencari.value = false;
        daftarAnggota.value = props.members.data ?? [];
        return;
    }

    sudahMencari.value = true;

    // Debounce 300 milidetik
    timer = setTimeout(async () => {
        controller = new AbortController();
        sedangMencari.value = true;

        try {
            const response = await axios.get('/members/search', {
                params: {
                    q: keyword.value,
                },
                signal: controller.signal,
            });

            daftarAnggota.value = response.data;
        } catch (error) {
            if (!axios.isCancel(error)) {
                pesanError.value = 'Gagal mengambil data anggota.';
            }
        } finally {
            sedangMencari.value = false;
        }
    }, 300);
}

function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id));
    }
}

onBeforeUnmount(() => {
    clearTimeout(timer);

    if (controller) {
        controller.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-4">
            <h1 class="text-xl font-bold">Data Anggota</h1>

            <div
                v-if="$page.props.flash.success"
                class="my-2 text-green-700"
            >
                {{ $page.props.flash.success }}
            </div>

            <div class="my-3 flex items-center gap-3">
                <Link :href="route('members.create')">
                    + Tambah Anggota
                </Link>

                <input
                    v-model="keyword"
                    @input="cari"
                    type="text"
                    placeholder="Cari nama atau NIS/NIM..."
                    class="rounded border px-3 py-2"
                >
            </div>

            <p v-if="sedangMencari" class="my-2 text-gray-500">
                Sedang mencari anggota...
            </p>

            <p v-if="pesanError" class="my-2 text-red-600">
                {{ pesanError }}
            </p>

            <table class="border-collapse">
                <thead>
                    <tr>
                        <th class="border px-3 py-2">Foto</th>
                        <th class="border px-3 py-2">NIS/NIM</th>
                        <th class="border px-3 py-2">Nama</th>
                        <th class="border px-3 py-2">No. HP</th>
                        <th class="border px-3 py-2">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="member in daftarAnggota"
                        :key="member.id"
                    >
                        <td class="border px-3 py-2">
                            <img
                                v-if="member.foto"
                                :src="`/storage/${member.foto}`"
                                alt="Foto anggota"
                                class="h-16 w-16 object-cover"
                            >
                            <span v-else>-</span>
                        </td>

                        <td class="border px-3 py-2">
                            {{ member.nis_nim }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ member.nama }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ member.no_hp }}
                        </td>

                        <td class="border px-3 py-2">
                            <Link
                                :href="route('members.edit', member.id)"
                                class="mr-3"
                            >
                                Edit
                            </Link>

                            <button @click="hapus(member.id)">
                                Hapus
                            </button>
                        </td>
                    </tr>

                    <tr
                        v-if="daftarAnggota.length === 0 && !sedangMencari"
                    >
                        <td colspan="5" class="border px-3 py-2">
                            {{
                                sudahMencari
                                    ? 'Anggota tidak ditemukan.'
                                    : 'Belum ada data anggota.'
                            }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination hanya ditampilkan saat tidak mencari -->
            <div
                v-if="!sudahMencari"
                class="mt-3 flex gap-3"
            >
                <template
                    v-for="link in members.links"
                    :key="link.label"
                >
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="link.label"
                    />

                    <span
                        v-else
                        v-html="link.label"
                        class="text-gray-400"
                    />
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
