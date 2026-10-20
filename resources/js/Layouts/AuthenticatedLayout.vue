<script setup>
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="flex min-h-screen">
            <Sidebar />

            <div class="min-w-0 flex-1">
                <header
                    class="flex h-10 items-center justify-end border-b border-gray-300 bg-white px-4"
                >
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="text-xs text-gray-700 hover:text-gray-900"
                            >
                                {{ $page.props.auth.user.name }} ⌄
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')">
                                Profile
                            </DropdownLink>

                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </header>

                <main class="p-0">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
