import './bootstrap';
import { createApp } from 'vue';
import HoldingDashboardWidgets from './components/HoldingDashboardWidgets.vue';

const holdingDashboardWidgets = document.getElementById('holding-dashboard-widgets');

if (holdingDashboardWidgets) {
    createApp(HoldingDashboardWidgets, {
        revenueToday: holdingDashboardWidgets.dataset.revenueToday,
        pendingApprovals: holdingDashboardWidgets.dataset.pendingApprovals,
        criticalStock: holdingDashboardWidgets.dataset.criticalStock,
        activeBrands: holdingDashboardWidgets.dataset.activeBrands,
        unreadNotifications: holdingDashboardWidgets.dataset.unreadNotifications,
    }).mount(holdingDashboardWidgets);
}

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js');
    });
}
