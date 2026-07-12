<script setup>
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';


const props = defineProps({
    friends: Array,
    initialMessages: Array
});

let formtimeout = null;
const searchQuery = ref('');
let searchResponse = ref([]);

function searchUser() {
    const cleanValue = searchQuery.value.replace(/\s/g, "");

    clearTimeout(formtimeout);

    if (cleanValue.length > 2) {
        formtimeout = setTimeout(async () => {
            try {
                searchResponse = await axios.post(route('user.search'), {
                    username: cleanValue
                });
            } catch (error) {
                console.error(error);

            }
        }, 300);
    }
}

const activeTab = ref('chats');
</script>

<template>
    <div
        class="w-1/3 w-max-[350px] bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
        <div
            class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mx-1 hover:underline cursor-pointer"
                :class="{ 'active underline': activeTab === 'chats' }" @click="activeTab = 'chats'">
                Chats
            </h2>
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mx-1 hover:underline cursor-pointer js-chat-aside"
                :class="{ 'active underline': activeTab === 'users' }" @click="activeTab = 'users'">
                Users
            </h2>
        </div>



        <div v-if="activeTab === 'chats'" class="flex-1 overflow-y-auto">
            <div v-for="friend in friends" :key="friend.id" @click="selectFriend(friend)" :class="['flex items-center p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition',
                activeFriend?.id === friend.id ? 'bg-gray-100 dark:bg-gray-700' : '']">
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                    {{ friend.first_name[0] }}{{ friend.last_name[0] }}
                </div>

                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ friend.first_name }} {{ friend.last_name }}
                    </h4>
                    <p class="text-xs text-gray-500 truncate">Click to start chatting...</p>
                </div>
            </div>

            <div v-if="friends.length === 0" class="p-4 text-center text-gray-500 text-sm">
                No friends added yet.
            </div>
        </div>

        <div v-else class="flex-1 overflow-y-auto">
            <div>
                <TextInput v-model="searchQuery" type="search" placeholder="Search users by email"
                    class="w-[90%] mx-4 my-2" @input="searchUser" />
            </div>

            <div v-for="user in searchResponse.data" :key="user.id" class="flex items-center my-4 mx-2">
                <div
                    class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white font-semibold mr-3">
                    {{ user.first_name[0] }}{{ user.last_name[0] }}
                </div>

                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ user.first_name }} {{ user.last_name }}
                    </h4>
                </div>

                <div>

                    <i class="fa-regular fa-check"></i>

                </div>
            </div>

            <div v-if="searchResponse.length === 0" class="p-4 text-center text-gray-500 text-sm">
                No user found.
            </div>
        </div>

    </div>
</template>