<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const activeFriend = ref(null);
const messages = ref(props.initialMessages || []);

const messageForm = useForm({
    message: '',
    receiver_id: null
});

// Select a friend to start chatting
const selectFriend = (friend) => {
    activeFriend.value = friend;
    messageForm.receiver_id = friend.id;

    // In a full implementation, you'd trigger an Inertia visit here to fetch 
    // history or setup a WebSocket listener for this specific channel.
};

// Send Text Message Action
const sendMessage = () => {
    if (!messageForm.message.trim() || !activeFriend.value) return;

    // Optimistically push message straight to UI screen for instant rendering speed
    messages.value.push({
        id: Date.now(),
        sender_id: '$page.props.auth.user.id', // Conceptual local reference
        message: messageForm.message,
        created_at: new Date().toISOString()
    });

    messageForm.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            messageForm.reset('message');
        }
    });
};
</script>

<template>
    <div class="flex-1 flex flex-col bg-slate-50 dark:bg-gray-900">
        <template v-if="activeFriend">
            <!-- Chat Window Header -->
            <div class="p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center">
                <div
                    class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                    {{ activeFriend.first_name[0] }}{{ activeFriend.last_name[0] }}
                </div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">
                    {{ activeFriend.first_name }} {{ activeFriend.last_name }}
                </h3>
            </div>

            <!-- Messages Stream View Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <div v-for="msg in messages" :key="msg.id" :class="['flex max-w-[70%] rounded-lg p-3 text-sm shadow-sm',
                    msg.sender_id === activeFriend.id
                        ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 mr-auto'
                        : 'bg-indigo-600 text-white ml-auto']">
                    <p>{{ msg.message }}</p>
                </div>
            </div>

            <!-- Text Dispatch Console Footer Bar -->
            <div class="p-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input v-model="messageForm.message" type="text" placeholder="Type a message..."
                        class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md" />
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm transition">
                        Send
                    </button>
                </form>
            </div>
        </template>

        <!-- Default Landing Screen (No Chat Active) -->
        <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-16 h-16 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a.75.75 0 0 1-1.074-.765 5.99 5.99 0 0 1 1.405-3.233C3.222 15.658 2 13.93 2 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
            </svg>
            <p class="text-base">Select a conversation to begin messaging</p>
        </div>
    </div>
</template>