<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ShieldCheck, FolderKanban, Paperclip, History, FileSpreadsheet } from '@lucide/vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import AppButton from '@/Components/AppButton.vue';

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
        icon: ShieldCheck,
        title: 'Role & User Management',
        description: 'Atur akses berbasis role custom — CRUD role dan user account, khusus Administrator untuk kelola user.',
    },
    {
        icon: FolderKanban,
        title: 'Project & Task Tracking',
        description: 'Kelola project dan task dengan status, prioritas, due date, dan assignee — pencarian & pagination server-side.',
    },
    {
        icon: Paperclip,
        title: 'File Attachment',
        description: 'Upload lampiran per task dengan validasi ukuran dan status verifikasi.',
    },
    {
        icon: History,
        title: 'Audit Trail',
        description: 'Setiap perubahan pada project, task, dan attachment tercatat lengkap — siapa, kapan, apa yang berubah.',
    },
    {
        icon: FileSpreadsheet,
        title: 'Import / Export Excel',
        description: 'Export task dengan kolom pilihan sendiri, import backlog dari file Excel/CSV dengan mapping kolom dinamis — diproses via queue.',
    },
];
</script>

<template>
    <Head title="Task Management" />

    <div class="min-h-screen bg-app font-sans text-ink">
        <header class="mx-auto max-w-6xl px-6 py-8">
            <nav class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <ApplicationLogo :size="34" />
                    <span class="font-display text-lg font-semibold tracking-tight">Task Management</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-3">
                    <AppButton v-if="$page.props.auth.user" :href="route('dashboard')" variant="secondary">
                        Dashboard
                    </AppButton>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="rounded-control px-3 py-2 text-sm font-semibold text-secondary transition-colors duration-150 ease-out hover:text-ink"
                        >
                            Masuk
                        </Link>
                        <AppButton v-if="canRegister" :href="route('register')">
                            Daftar
                        </AppButton>
                    </template>
                </div>
            </nav>
        </header>

        <main class="mx-auto max-w-6xl px-6">
            <section class="py-16 text-center sm:py-24">
                <h1 class="mx-auto max-w-3xl font-display text-4xl font-semibold tracking-tight text-ink sm:text-5xl">
                    Kelola Project dan Task, Tanpa Ribet
                </h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg text-secondary">
                    Role & user management, tracking task per project, riwayat audit lengkap,
                    dan import/export Excel — semua dalam satu aplikasi.
                </p>
                <div class="mt-8 flex justify-center gap-3">
                    <AppButton v-if="!$page.props.auth.user" :href="route('login')" class="!px-6 !py-3">
                        Mulai Sekarang
                    </AppButton>
                    <AppButton v-else :href="route('dashboard')" class="!px-6 !py-3">
                        Buka Dashboard
                    </AppButton>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-5 pb-24 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(feature, index) in features"
                    :key="feature.title"
                    class="animate-stagger-in rounded-card border border-border bg-white p-6 shadow-card"
                    :style="{ animationDelay: `${index * 50}ms` }"
                >
                    <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-control bg-accent-tint text-accent-dark">
                        <component :is="feature.icon" :size="18" :stroke-width="2.25" />
                    </div>
                    <h2 class="font-display text-base font-semibold text-ink">
                        {{ feature.title }}
                    </h2>
                    <p class="mt-1.5 text-sm text-secondary">
                        {{ feature.description }}
                    </p>
                </div>
            </section>
        </main>

        <footer class="border-t border-border py-8 text-center text-sm text-secondary">
            Task Management App
        </footer>
    </div>
</template>
