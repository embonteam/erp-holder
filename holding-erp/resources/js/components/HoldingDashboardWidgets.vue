<script setup>
import DashboardStatus from './DashboardStatus.vue';

defineProps({
    revenueToday: {
        type: String,
        required: true,
    },
    pendingApprovals: {
        type: String,
        required: true,
    },
    criticalStock: {
        type: String,
        required: true,
    },
    activeBrands: {
        type: String,
        required: true,
    },
    unreadNotifications: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <section class="erp-panel-grid">
        <DashboardStatus label="Revenue Today" :value="revenueToday" tone="success" />
        <DashboardStatus label="Pending Approvals" :value="pendingApprovals" tone="warning" />
        <DashboardStatus label="Critical Stock" :value="criticalStock" tone="danger" />
        <DashboardStatus label="Active Brands" :value="activeBrands" />
        <DashboardStatus label="Unread Notifications" :value="unreadNotifications" />
    </section>
</template>
