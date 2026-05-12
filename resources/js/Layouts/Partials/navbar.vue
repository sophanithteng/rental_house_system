<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineEmits(['toggle-sidebar']);
</script>

<template>
    <div class="flex flex-1 flex-col overflow-hidden transition-all duration-300 ease-in-out">
        <!-- Top Navbar -->
        <header class="flex h-16 shrink-0 items-center justify-between border-b bg-white px-4 shadow-sm lg:px-8">
            <div class="flex items-center gap-4">
                <!-- Sidebar Toggle Button -->
                <button @click="$emit('toggle-sidebar')"
                    class="rounded-md p-2 text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h2 class="text-lg font-semibold text-gray-800" v-if="$slots.header">
                    <slot name="header" />
                </h2>
            </div>

            <div class="flex items-center gap-4">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button type="button"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-800 focus:outline-none">
                            <span>{{ $page.props.auth.user.username }}</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                        <div class="border-t border-gray-100"></div>
                        <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto bg-gray-100 p-4 sm:p-6 lg:p-8">
            <!-- Flash Notification Display -->
            <div class="mx-auto w-full max-w-7xl">
                <div v-if="$page.props.flash.success"
                    class="mb-4 rounded-lg border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $page.props.flash.success }}
                    </div>
                </div>

                <div v-if="$page.props.flash.error"
                    class="mb-4 rounded-lg border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ $page.props.flash.error }}
                    </div>
                </div>
            </div>

            <slot />
        </main>
    </div>
</template>
