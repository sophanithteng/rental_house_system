<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { ref } from "vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    history: {
        type: Array,
        default: () => [],
    },
});

const profileForm = useForm({
    username: props.user.username,
    password: '',
    password_confirmation: '',
});

const activeView = ref('history');

const photoInput = ref(null);
const photoPreview = ref(null);
const photoForm = useForm({
    photo: null,
});

const triggerPhotoUpload = () => {
    photoInput.value.click();
};

const handleImageClick = () => {
    if (!photoForm.processing) {
        triggerPhotoUpload();
    }
};

const uploadPhoto = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    photoForm.photo = file;
    photoPreview.value = URL.createObjectURL(file);

    Swal.fire({
        title: "Update Profile Photo?",
        text: "Do you want to save this new image as your profile picture?",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#4a6755",
        cancelButtonColor: "#6b7280",
        confirmButtonText: "Yes, upload it!",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            photoForm.post(route("profile.photo"), {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => {
                    photoPreview.value = null;
                    Swal.fire({
                        title: "Success!",
                        text: "Profile photo updated successfully.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
            });
        } else {
            photoPreview.value = null;
            photoForm.reset();
        }
    });
};

const updateProfile = () => {
    profileForm.patch(route("profile.update"), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.reset('password', 'password_confirmation');
            Swal.fire({
                title: "Success!",
                text: "Profile updated successfully.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
            });
        },
    });
};

const deleteAccount = () => {
    let timerInterval;
    Swal.fire({
        title: "Are you sure?",
        html: "Once deleted, you will be redirected to the register page. This action is permanent!<br><br>The delete button will be active in <b>5</b> seconds.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        confirmButtonText: "Yes, delete it!",
        didOpen: () => {
            const confirmButton = Swal.getConfirmButton();
            confirmButton.disabled = true;
            let secondsLeft = 5;
            const countdownElement = Swal.getHtmlContainer().querySelector("b");

            timerInterval = setInterval(() => {
                secondsLeft--;
                if (countdownElement) countdownElement.textContent = secondsLeft;

                if (secondsLeft <= 0) {
                    clearInterval(timerInterval);
                    confirmButton.disabled = false;
                    if (countdownElement) countdownElement.parentElement.innerHTML = "You can now delete your account.";
                }
            }, 1000);
        },
        willClose: () => {
            clearInterval(timerInterval);
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("profile.destroy"), {
                onSuccess: () => {
                    router.visit(route('register'));
                },
            });
        }
    });
};

const imageUrl = (path) => {
    if (!path) return "/images/emptyuser.png";
    if (path.startsWith("http")) return path;

    const cleanPath = path.replace(/^public\//, "");
    return `/storage/${cleanPath}`;
};
</script>

<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 items-start">
                    <div class="lg:col-span-5 flex justify-center lg:justify-start">
                        <div
                            class="relative w-full max-w-md overflow-hidden rounded-[50px] bg-[#4a6755] shadow-2xl transition-all hover:shadow-green-900/20">
                            <div class="p-4 relative group cursor-pointer" @click="handleImageClick">
                                <img :src="photoPreview
                                    ? photoPreview
                                    : imageUrl(user.photo)
                                    "
                                    class="h-[300px] md:h-[450px] w-full rounded-[40px] object-cover object-top transition-opacity group-hover:opacity-90"
                                    alt="profile" />
                                <div
                                    class="absolute inset-4 rounded-[40px] bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white font-bold flex items-center gap-2">
                                        📷 Change Photo
                                    </span>
                                </div>

                                <!-- Loading Spinner Overlay -->
                                <div v-if="photoForm.processing"
                                    class="absolute inset-4 rounded-[40px] bg-black/60 flex flex-col items-center justify-center z-10">
                                    <svg class="animate-spin h-12 w-12 text-white mb-3"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span class="text-white font-medium">Uploading...</span>
                                </div>

                                <!-- Progress Bar -->
                                <div v-if="photoForm.progress" class="absolute bottom-8 left-10 right-10 z-20">
                                    <div class="w-full bg-gray-200/20 rounded-full h-2.5">
                                        <div class="bg-green-400 h-2.5 rounded-full transition-all duration-300" :style="{
                                            width:
                                                photoForm.progress
                                                    .percentage + '%',
                                        }"></div>
                                    </div>
                                </div>

                                <input ref="photoInput" type="file" class="hidden" @change="uploadPhoto"
                                    accept="image/*" />
                            </div>

                            <div class="px-10 pb-12 pt-6 text-white">
                                <div class="flex items-center gap-3 mb-4">
                                    <h1 class="text-4xl font-bold tracking-tight">
                                        {{ user.username }}
                                    </h1>
                                    <svg class="h-7 w-7 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.64.3 1.248.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                    </svg>
                                </div>

                                <p class="text-lg font-light text-gray-100/90 leading-relaxed mb-10 max-w-[90%]">
                                    Product designer who focuses on simplicity
                                    and usability
                                </p>

                                <div class="flex items-center justify-between">
                                    <button
                                        @click="activeView = 'edit'"
                                        class="rounded-full bg-white px-10 py-4 font-bold text-gray-900 shadow-xl transition-transform active:scale-95 hover:bg-gray-50">
                                        Edit
                                    </button>
                                    <button
                                        @click="activeView = 'delete'"
                                        class="rounded-full bg-white px-10 py-4 font-bold text-gray-900 shadow-xl transition-transform active:scale-95 hover:bg-gray-50">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-8">
                        <div class="rounded-[40px] bg-white p-10 shadow-sm border border-gray-100">
                            <!-- History View -->
                            <div v-if="activeView === 'history'">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-2xl font-bold text-gray-900">History</h3>
                                    <button class="text-sm font-semibold text-indigo-600 hover:underline">View All</button>
                                </div>

                                <div class="space-y-4">
                                    <div v-for="item in history" :key="item.id"
                                        class="flex items-center justify-between p-6 rounded-3xl bg-gray-50/50 border border-gray-100 transition-colors hover:bg-gray-50">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-2xl bg-white shadow-sm flex items-center justify-center text-xl">📅</div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ item.service }}</p>
                                                <p class="text-sm text-gray-500 font-medium">{{ item.date }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-black text-gray-900 text-lg">{{ item.amount }}</p>
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-700 uppercase tracking-wider">
                                                {{ item.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit View -->
                            <div v-else-if="activeView === 'edit'">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-2xl font-bold text-gray-900">Edit Profile</h3>
                                    <button @click="activeView = 'history'" class="text-sm font-semibold text-gray-500 hover:underline">
                                        Back to History
                                    </button>
                                </div>
                                <form @submit.prevent="updateProfile" class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                                        <input v-model="profileForm.username" type="text"
                                            class="w-full rounded-2xl border-gray-200 py-3 focus:ring-green-500 focus:border-green-500"
                                            placeholder="Username" />
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                            <input v-model="profileForm.password" type="password"
                                                class="w-full rounded-2xl border-gray-200 py-3 focus:ring-green-500 focus:border-green-500"
                                                placeholder="Leave blank to keep old one" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                            <input v-model="profileForm.password_confirmation" type="password"
                                                class="w-full rounded-2xl border-gray-200 py-3 focus:ring-green-500 focus:border-green-500"
                                                placeholder="Confirm new password" />
                                        </div>
                                    </div>
                                    <button
                                        :disabled="profileForm.processing"
                                        class="w-full rounded-2xl bg-[#4a6755] py-4 text-white font-bold transition hover:bg-green-800 disabled:opacity-50">
                                        {{ profileForm.processing ? 'Saving...' : 'Update Profile' }}
                                    </button>
                                </form>
                            </div>

                            <!-- Delete View -->
                            <div v-else-if="activeView === 'delete'" class="text-center py-10">
                                <div class="h-20 w-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="h-10 w-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Account</h3>
                                <p class="text-gray-500 mb-8 max-w-sm mx-auto">
                                    Are you sure you want to delete your account? This action cannot be undone and you will be redirected to the register page.
                                </p>
                                <div class="flex flex-col gap-3 max-w-xs mx-auto">
                                    <button
                                        @click="deleteAccount"
                                        class="w-full rounded-2xl bg-red-600 py-4 text-white font-bold transition hover:bg-red-700">
                                        Delete Account
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
