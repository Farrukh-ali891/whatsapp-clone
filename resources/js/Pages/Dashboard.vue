<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ChatList from './Chat/ChatList.vue';
import ChatMessage from './Chat/ChatMessage.vue';

const props = defineProps({
    friends: Array,
    initialMessages: Array
});

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900 overflow-hidden">
            <!-- Left Sidebar: Friends List -->
            <ChatList :friends="friends" :initialMessages="initialMessages" />

            <!-- Right Window: Chat History Panel -->
        </div>
    </AuthenticatedLayout>
</template>
