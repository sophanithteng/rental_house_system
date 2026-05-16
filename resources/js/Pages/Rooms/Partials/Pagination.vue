<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex flex-wrap items-center justify-center gap-1 mt-6">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null"
                 class="px-4 py-2 text-sm leading-4 text-slate-400 border border-slate-200 rounded-lg cursor-default"
                 v-html="link.label" />
            <Link v-else
                  :href="link.url"
                  class="px-4 py-2 text-sm font-medium leading-4 border rounded-lg transition-all duration-200 hover:bg-indigo-50"
                  :class="{
                      'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700': link.active,
                      'bg-white text-slate-700 border-slate-200': !link.active
                  }"
                  v-html="link.label" />
        </template>
    </div>
</template>
