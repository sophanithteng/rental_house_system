<script setup>
import { Link } from '@inertiajs/vue3';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

defineProps({
    showingNavigationDropdown: Boolean,
    navItems: Array,
    adminNavItems: Array,
});

defineEmits(['toggle-sidebar']);
</script>

<template>
    <!-- Mobile Backdrop: Closes sidebar when clicking outside on mobile -->
    <div
        v-if="showingNavigationDropdown"
        @click="$emit('toggle-sidebar')"
        class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-[2px] lg:hidden transition-opacity cursor-pointer"
    ></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 transform bg-slate-900 transition-all duration-300 ease-in-out lg:static overflow-hidden"
        :class="showingNavigationDropdown
            ? 'w-64 translate-x-0'
            : 'w-64 -translate-x-full lg:w-20 lg:translate-x-0'">

        <div class="flex h-16 items-center border-b border-slate-700 bg-slate-900 px-4 transition-all duration-300"
            :class="showingNavigationDropdown ? 'justify-start px-6' : 'lg:justify-center lg:px-0'">
            <Link :href="route('dashboard')" class="flex items-center"
                :class="showingNavigationDropdown ? 'gap-3' : ''">
                <img src="/images/logo.png" alt="Logo" class="h-9 w-9 shrink-0 object-contain shadow-sm">
                <span v-show="showingNavigationDropdown"
                    class="text-xl font-bold tracking-wider text-white uppercase whitespace-nowrap">Rental System</span>
            </Link>
        </div>

        <Link :href="route('profile.edit')"
            :title="!showingNavigationDropdown ? $page.props.auth.user.username : ''"
            class="flex items-center border-b border-slate-700 px-4 py-4 transition-all duration-300 hover:bg-slate-800"
            :class="showingNavigationDropdown ? 'justify-start gap-4 px-6' : 'lg:justify-center lg:px-0'">
            <div class="relative shrink-0">
                <img class="h-9 w-9 rounded-full object-cover border-2 border-slate-600"
                    :src="$page.props.auth.user.photo ? `/storage/${$page.props.auth.user.photo}` : '/Images/emptyuser.png'"
                    alt="User Avatar" />
                <span class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-slate-900"></span>
            </div>
            <div v-show="showingNavigationDropdown" class="flex flex-col overflow-hidden">
                <span class="text-sm font-semibold text-white truncate">{{ $page.props.auth.user.username }}</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-xs text-slate-400">Online</span>
                </div>
            </div>
        </Link>

        <nav class="mt-2 flex-1 space-y-1 px-2">
            <div v-show="showingNavigationDropdown"
                class="px-3 py-2 text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Main Menu</div>
            <ResponsiveNavLink v-for="item in navItems" :key="item.route" :href="route(item.route)"
                :active="route().current(item.route)"
                class="flex items-center rounded-md px-3 py-2 text-slate-300 transition-all hover:bg-slate-800 hover:text-white"
                :class="showingNavigationDropdown ? 'gap-3' : 'lg:justify-center lg:gap-0'">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path :d="item.icon" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span v-show="showingNavigationDropdown" class="text-sm font-medium whitespace-nowrap">{{ item.name }}</span>
            </ResponsiveNavLink>

            <template v-if="$page.props.auth.user?.role === 'Admin' || $page.props.auth.user?.role === 'SuperAdmin'">
                <div v-show="showingNavigationDropdown"
                    class="mt-6 px-3 py-2 text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">
                    Administration</div>
                <ResponsiveNavLink v-for="item in adminNavItems" :key="item.route" :href="route(item.route)"
                    :active="route().current(item.route)"
                    class="flex items-center rounded-md px-3 py-2 text-slate-300 transition-all hover:bg-slate-800 hover:text-white"
                    :class="showingNavigationDropdown ? 'gap-3' : 'lg:justify-center lg:gap-0'">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path :d="item.icon" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span v-show="showingNavigationDropdown" class="text-sm font-medium whitespace-nowrap">{{ item.name }}</span>
                </ResponsiveNavLink>
            </template>

            <div v-show="showingNavigationDropdown"
                class="mt-6 px-3 py-2 text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Account</div>

            <ResponsiveNavLink :href="route('logout')" method="post" as="button"
                class="w-full flex items-center rounded-md px-3 py-2 text-slate-300 transition-all hover:bg-slate-800 hover:text-white text-left"
                :class="showingNavigationDropdown ? 'gap-3' : 'lg:justify-center lg:gap-0'">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span v-show="showingNavigationDropdown" class="text-sm font-medium whitespace-nowrap">Log Out</span>
            </ResponsiveNavLink>
        </nav>
    </aside>
</template>
