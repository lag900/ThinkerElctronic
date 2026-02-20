import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

export const system = reactive({
    isLoading: false,
    isProcessing: false,
    notifications: [],

    setLoading(value) {
        this.isLoading = value;
    },

    setProcessing(value) {
        this.isProcessing = value;
    },

    notify(message, type = 'success') {
        const id = Date.now();
        this.notifications.push({ id, message, type });
        setTimeout(() => {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }, 5000);
    }
});

// Global Inertia Listeners for Loading States
router.on('start', () => system.setLoading(true));
router.on('finish', () => system.setLoading(false));
router.on('error', () => {
    system.setLoading(false);
    system.notify('An error occurred. Please try again.', 'error');
});
router.on('success', (event) => {
    const flash = event.detail.page.props.flash;
    if (flash?.success) system.notify(flash.success);
    if (flash?.error) system.notify(flash.error, 'error');
});
