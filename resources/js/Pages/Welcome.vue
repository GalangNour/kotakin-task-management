<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const features = [
    {
        title: 'Role & User Management',
        description: 'Atur akses berbasis role custom — CRUD role dan user account, khusus Administrator untuk kelola user.',
    },
    {
        title: 'Project & Task Tracking',
        description: 'Kelola project dan task dengan status, prioritas, due date, dan assignee — pencarian & pagination server-side.',
    },
    {
        title: 'File Attachment',
        description: 'Upload lampiran per task dengan validasi ukuran dan status verifikasi.',
    },
    {
        title: 'Audit Trail',
        description: 'Setiap perubahan pada project, task, dan attachment tercatat lengkap — siapa, kapan, apa yang berubah.',
    },
    {
        title: 'Import / Export Excel',
        description: 'Export task dengan kolom pilihan sendiri, import backlog dari file Excel/CSV dengan mapping kolom dinamis — diproses via queue.',
    },
];
</script>

<template>
    <Head title="Task Management" />

    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <header class="mx-auto max-w-7xl px-6 py-8">
            <nav class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <ApplicationLogo class="h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <span class="text-lg font-semibold text-gray-800 dark:text-gray-200">Task Management</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-md px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
                        >
                            Masuk
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                        >
                            Daftar
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-7xl px-6">
            <section class="py-16 text-center sm:py-24">
                <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl">
                    Kelola Project dan Task, Tanpa Ribet
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                    Role & user management, tracking task per project, riwayat audit lengkap,
                    dan import/export Excel — semua dalam satu aplikasi.
                </p>
                <div class="mt-8 flex justify-center gap-3">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="route('login')"
                        class="rounded-md bg-gray-800 px-6 py-3 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        Mulai Sekarang
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="rounded-md bg-gray-800 px-6 py-3 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                    >
                        Buka Dashboard
                    </Link>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 pb-24 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700"
                >
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ feature.title }}
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ feature.description }}
                    </p>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 py-8 text-center text-sm text-gray-500 dark:border-gray-800 dark:text-gray-500">
            Task Management App
        </footer>
    </div>
</template>
