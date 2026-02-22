<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import { cart as cartStore } from '@/Stores/CartStore';
import { system } from '@/Stores/SystemStore';
import axios from 'axios';
import debounce from 'lodash/debounce';

const props = defineProps({
    products: Array,
    initial_customers: Array,
});

const searchQuery = ref('');
const searchResults = ref([]);
const cart = ref([]);
const customer_id = ref(null);
const selectedCustomer = ref(null);
const customerStats = ref(null);
const customerSearchQuery = ref('');
const customerSearchResults = ref([]);
const showCustomerModal = ref(false);
const showHistoryModal = ref(false);
const showReceiptModal = ref(false);
const lastCompletedInvoice = ref(null);
const fullHistory = ref(null);

const payment_method = ref('cash');
const discount = ref(0);
const amount_paid = ref(0);
const isProcessing = ref(false);

const customerForm = useForm({
    name: '',
    phone: '',
    notes: '',
    email: '',
});

const subtotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const total = computed(() => {
    return Math.max(0, subtotal.value - discount.value);
});

const addToCart = (product) => {
    const existing = cart.value.find(item => item.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock_quantity) {
            existing.quantity++;
        } else {
            system.notify(cartStore.language === 'ar' ? 'عفواً، الكمية المطلوبة غير متوفرة في المخزن حالياً' : 'Insufficient stock', 'error');
        }
    } else {
        cart.value.push({
            id: product.id,
            name: product.name,
            price: product.effective_price || product.price,
            image_url: product.image_url,
            quantity: 1,
            stock: product.stock_quantity,
        });
    }
    searchQuery.value = '';
    searchResults.value = [];
};

const removeFromCart = (index) => {
    cart.value.splice(index, 1);
};

const updateQuantity = (item, amount) => {
    const newQty = item.quantity + amount;
    if (newQty > 0 && newQty <= item.stock) {
        item.quantity = newQty;
    }
};

const searchProducts = async () => {
    if (searchQuery.value.length < 1) {
        searchResults.value = [];
        return;
    }
    try {
        const res = await axios.get(route('admin.pos.search'), { params: { q: searchQuery.value } });
        searchResults.value = res.data;
        
        // Auto-add if exact SKU/Barcode match
        if (res.data.length === 1 && (res.data[0].sku === searchQuery.value || res.data[0].id.toString() === searchQuery.value)) {
            addToCart(res.data[0]);
        }
    } catch (e) {
        console.error(e);
    }
};

const searchCustomers = debounce(async () => {
    if (customerSearchQuery.value.length < 2) {
        customerSearchResults.value = [];
        return;
    }
    try {
        const res = await axios.get(route('admin.pos.customers.search'), { params: { q: customerSearchQuery.value } });
        customerSearchResults.value = res.data;
    } catch (e) {
        console.error(e);
    }
}, 300);

const selectCustomer = async (customer) => {
    customer_id.value = customer.id;
    selectedCustomer.value = customer;
    customerSearchQuery.value = customer.name;
    customerSearchResults.value = [];
    
    // Fetch Stats
    try {
        const res = await axios.get(route('admin.pos.customers.stats', customer.id));
        customerStats.value = res.data;
    } catch (e) {
        console.error(e);
    }
};

const walkInCustomer = async () => {
    try {
        const res = await axios.post(route('admin.pos.customers.store'), {
            name: 'Walk-in Customer',
            phone: '0000',
            notes: 'System default walk-in'
        });
        selectCustomer(res.data);
    } catch (e) {
        // If already exists, search and select
        const res = await axios.get(route('admin.pos.customers.search'), { params: { q: 'Walk-in' } });
        if (res.data.length > 0) selectCustomer(res.data[0]);
    }
};

const fetchHistory = async () => {
    if (!customer_id.value) return;
    try {
        const res = await axios.get(route('admin.pos.customers.history', customer_id.value));
        fullHistory.value = res.data;
        showHistoryModal.value = true;
    } catch (e) {
        console.error(e);
    }
};

const createCustomer = async () => {
    try {
        const res = await axios.post(route('admin.pos.customers.store'), customerForm.data());
        system.notify(cartStore.language === 'ar' ? 'تم قيد بيانات العميل الجديد في النظام' : 'Customer profile created successfully');
        selectCustomer(res.data);
        showCustomerModal.value = false;
        customerForm.reset();
    } catch (e) {
        system.notify(e.response?.data?.message || 'Error adding customer', 'error');
    }
};

const submitInvoice = async () => {
    if (!customer_id.value) {
        system.notify(cartStore.language === 'ar' ? 'يجب تحديد عميل أولاً لإكمال المعاملة' : 'Please select a customer first', 'error');
        return;
    }
    if (cart.value.length === 0) return;

    isProcessing.value = true;
    try {
        const res = await axios.post(route('admin.pos.invoice'), {
            items: cart.value,
            customer_id: customer_id.value,
            payment_method: payment_method.value,
            amount_paid: amount_paid.value || total.value, // Default to full amount
            discount: discount.value,
        });

        if (res.data.success) {
            system.notify(cartStore.language === 'ar' ? 'تم إصدار الفاتورة بنجاح' : 'Invoice created successfully');
            lastCompletedInvoice.value = res.data;
            showReceiptModal.value = true;
            
            // Reset POS
            cart.value = [];
            customer_id.value = null;
            selectedCustomer.value = null;
            customerStats.value = null;
            customerSearchQuery.value = '';
            amount_paid.value = 0;
            discount.value = 0;
        }
    } catch (e) {
        system.notify(e.response?.data?.message || 'Error generating invoice', 'error');
    } finally {
        isProcessing.value = false;
    }
};

const printReceipt = () => {
    window.print();
};

watch(searchQuery, searchProducts);
watch(customerSearchQuery, searchCustomers);

onMounted(() => {
    document.getElementById('pos-search')?.focus();
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cartStore.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const paymentMethods = [
    { key: 'cash', ar: 'كاش', en: 'Cash' },
    { key: 'card', ar: 'فيزا / POS', en: 'Card' },
    { key: 'transfer', ar: 'تحويل / محفظة', en: 'Transfer' }
];
</script>

<template>
    <Head :title="cartStore.language === 'ar' ? 'نقطة البيع (POS)' : 'Point of Sale'" />

    <AdminLayout>
        <template #header>{{ cartStore.language === 'ar' ? 'نقطة البيع' : 'Point of Sale' }}</template>

        <div class="flex h-[calc(100vh-96px)] bg-[#f8f9fb] -m-12 overflow-hidden" :dir="cartStore.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Left Sector: Catalog (Products) -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Search Section -->
                <div class="p-6 pb-0">
                    <div class="relative group">
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#ff2e63] transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <input 
                            v-model="searchQuery"
                            id="pos-search"
                            type="text" 
                            class="w-full bg-white border border-[#ececf2] rounded-[16px] py-4 pr-14 pl-6 text-lg font-bold shadow-sm focus:ring-4 focus:ring-[#ff2e63]/5 focus:border-[#ff2e63] placeholder:text-gray-400 transition-all"
                            :placeholder="cartStore.language === 'ar' ? 'ابحث بالاسم أو الباركود...' : 'Search for items...'"
                            autocomplete="off"
                        >
                        <!-- Search Results Dropdown -->
                        <div v-if="searchResults.length > 0" class="absolute top-full left-0 w-full bg-white shadow-2xl rounded-[24px] mt-2 z-[100] max-h-96 overflow-y-auto border border-[#ececf2] p-2 space-y-1">
                            <div 
                                v-for="p in searchResults" 
                                :key="p.id"
                                @click="addToCart(p)"
                                class="p-3 hover:bg-[#f8f9fb] cursor-pointer flex items-center justify-between rounded-[16px] border border-transparent hover:border-[#ececf2] transition-all group"
                            >
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-[#f8f9fb] rounded-[12px] flex items-center justify-center overflow-hidden border border-[#ececf2]">
                                        <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-cover">
                                        <span v-else class="text-sm font-black text-gray-300">{{ p.name.charAt(0) }}</span>
                                    </div>
                                    <div :class="cartStore.language === 'ar' ? 'text-right' : 'text-left'">
                                        <p class="font-bold text-[#1f1f1f] text-sm tracking-tight">{{ p.name }}</p>
                                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-0.5">STOCK: {{ p.stock_quantity }}</p>
                                    </div>
                                </div>
                                <p class="font-black text-[#ff2e63] text-base tracking-tighter">{{ formatCurrency(p.effective_price || p.price) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Grid Scrollable Area -->
                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
                    <div class="grid grid-cols-[repeat(auto-fill,minmax(220px,1fr))] gap-5 align-content-start">
                        <div 
                            v-for="p in products" 
                            :key="p.id"
                            @click="addToCart(p)"
                            class="bg-white p-3 rounded-[20px] border border-[#ececf2] shadow-sm hover:shadow-md transition-all cursor-pointer group flex flex-col relative overflow-hidden"
                        >
                            <div class="w-full h-[140px] bg-[#f8f9fb] rounded-[16px] mb-3 overflow-hidden relative border border-[#ececf2] p-3">
                                <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-contain transition-transform duration-700 group-hover:scale-105 mx-auto">
                                <div v-else class="w-full h-full flex items-center justify-center text-3xl font-black text-gray-200">{{ p.name.charAt(0) }}</div>
                                
                                <div class="absolute top-2 left-2 z-10 flex flex-col gap-1.5" :class="cartStore.language === 'ar' ? 'left-auto right-2' : 'left-2'">
                                    <span class="bg-gray-900/90 backdrop-blur-sm text-white text-[9px] font-black px-2 py-0.5 rounded-lg tracking-wider uppercase">Qty: {{ p.stock_quantity }}</span>
                                    <span v-if="p.is_on_sale" class="bg-[#ff2e63] text-white text-[8px] font-black px-2 py-0.5 rounded-lg shadow-lg uppercase tracking-widest text-center animate-pulse">Sale</span>
                                </div>
                            </div>

                            <div class="flex-1 flex flex-col">
                                <h5 class="text-xs font-bold text-[#1a1a1a] leading-tight line-clamp-2 mb-3 h-[2.5em] group-hover:text-[#ff2e63] transition-colors">{{ p.name }}</h5>
                                <div class="flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[15px] font-black text-[#1f1f1f] tracking-tighter">{{ formatCurrency(p.effective_price || p.price) }}</span>
                                        <span v-if="p.is_on_sale" class="text-[10px] text-gray-400 line-through font-bold">{{ formatCurrency(p.price) }}</span>
                                    </div>
                                    <button class="w-full h-[40px] bg-[#ff2e63] text-white rounded-xl text-[11px] font-black uppercase tracking-wider hover:bg-[#e60045] hover:scale-[1.03] transition-all active:scale-95 shadow-sm">
                                        {{ cartStore.language === 'ar' ? 'إضافة للعملية' : 'Add to Order' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sector: Cart & Checkout (Fixed Sidebar) -->
            <div class="w-[380px] h-full flex flex-col bg-white border-x border-[#ececf2] overflow-hidden relative shrink-0">
                <div class="flex-none p-6 border-b border-[#f8f9fb]">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-black text-[#1f1f1f] tracking-tighter uppercase">{{ cartStore.language === 'ar' ? 'سلة المشتريات' : 'Current Order' }}</h4>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">{{ cart.length }} items selected</p>
                        </div>
                        <button v-if="cart.length > 0" @click="cart = []" class="px-3 py-1 bg-red-50 text-[#ff2e63] text-[9px] font-black uppercase rounded-lg hover:bg-red-100 transition-colors">Reset</button>
                    </div>
                </div>

                <!-- Scrollable Order Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                    <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center text-gray-200 gap-4 opacity-40">
                        <div class="w-16 h-16 bg-[#f8f9fb] rounded-[24px] flex items-center justify-center border-2 border-dashed border-[#ececf2]">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        </div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em]">{{ cartStore.language === 'ar' ? 'السلة فارغة' : 'Waiting for items' }}</p>
                    </div>
                    
                    <div v-for="(item, idx) in cart" :key="item.id" class="flex items-center gap-3 bg-[#f8f9fb] p-3 rounded-[16px] border border-[#ececf2] relative group">
                        <div class="w-12 h-12 bg-white rounded-xl overflow-hidden border border-[#ececf2] shrink-0">
                            <img v-if="item.image_url" :src="item.image_url" class="w-full h-full object-cover">
                            <div v-else class="w-full h-full flex items-center justify-center font-black text-gray-200 text-xs">{{ item.name.charAt(0) }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h6 class="text-xs font-bold text-[#1f1f1f] leading-tight truncate">{{ item.name }}</h6>
                            <p class="text-[10px] font-black text-[#ff2e63] mt-0.5">{{ formatCurrency(item.price) }}</p>
                        </div>
                        <div class="flex items-center bg-white rounded-lg p-0.5 border border-[#ececf2]">
                            <button @click="updateQuantity(item, -1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-[#ff2e63]">-</button>
                            <span class="text-[10px] font-black w-6 text-center text-[#1f1f1f]">{{ item.quantity }}</span>
                            <button @click="updateQuantity(item, 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-[#ff2e63]">+</button>
                        </div>
                        <button @click="removeFromCart(idx)" class="ml-1 text-gray-300 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Footer Checkout Controls -->
                <div class="flex-none p-6 bg-white border-t border-[#f8f9fb] space-y-4 shadow-[0_-10px_30px_rgba(0,0,0,0.02)]">
                    <!-- Customer Selector -->
                    <div class="space-y-2 relative">
                        <div class="flex items-center justify-between text-[9px] font-black uppercase tracking-widest text-gray-400 px-1">
                            <span>Customer Profile</span>
                            <div class="flex gap-3">
                                <button @click="walkInCustomer" class="text-[#ff2e63]">Walk-in</button>
                                <button @click="showCustomerModal = true" class="text-gray-900">+ New</button>
                            </div>
                        </div>
                        <div class="relative">
                            <input 
                                v-model="customerSearchQuery"
                                type="text" 
                                class="w-full bg-[#f8f9fb] border-[#ececf2] rounded-[12px] py-3 px-4 text-xs font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] placeholder:text-gray-300 transition-all" 
                                :placeholder="cartStore.language === 'ar' ? 'ابحث عن عميل...' : 'Search...'"
                            >
                            <div v-if="customerSearchResults.length > 0" class="absolute bottom-full mb-2 left-0 w-full bg-white shadow-2xl rounded-2xl z-[110] border border-[#ececf2] max-h-48 overflow-y-auto p-2">
                                <div 
                                    v-for="c in customerSearchResults" 
                                    :key="c.id"
                                    @click="selectCustomer(c)"
                                    class="p-2.5 hover:bg-[#f8f9fb] cursor-pointer rounded-xl transition-all"
                                >
                                    <p class="text-[11px] font-bold text-[#1f1f1f]">{{ c.name }}</p>
                                    <p class="text-[9px] font-black text-gray-400 mt-0.5 uppercase tracking-widest">{{ c.phone }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Active Customer Card -->
                        <div v-if="customerStats" class="bg-gray-900 text-white rounded-xl p-3 flex items-center justify-between animate-in slide-in-from-bottom-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center font-black text-xs">{{ customerStats.name.charAt(0) }}</div>
                                <div>
                                    <p class="text-[10px] font-bold truncate max-w-[140px]">{{ customerStats.name }}</p>
                                    <p class="text-[8px] text-red-400 font-bold">Debt: {{ formatCurrency(customerStats.debt) }}</p>
                                </div>
                            </div>
                            <button @click="fetchHistory" class="p-2 hover:bg-white/10 rounded-lg transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 8v4l3 2" /></svg></button>
                        </div>
                    </div>

                    <!-- Payment & Totals -->
                    <div class="grid grid-cols-2 gap-3">
                        <select v-model="payment_method" class="bg-[#f8f9fb] border-[#ececf2] rounded-[12px] py-3 px-3 text-[11px] font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] outline-none transition-all shadow-sm">
                            <option v-for="m in paymentMethods" :key="m.key" :value="m.key">{{ cartStore.language === 'ar' ? m.ar : m.en }}</option>
                        </select>
                        <input v-model.number="discount" type="number" class="bg-[#f8f9fb] border-[#ececf2] rounded-[12px] py-3 px-3 text-[11px] font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] shadow-sm" placeholder="Discount (EGP)">
                    </div>

                    <div class="pt-4 border-t border-[#f8f9fb] space-y-3">
                        <div class="flex items-center justify-between text-[11px] font-black uppercase text-gray-400 tracking-widest">
                            <span>Final Total</span>
                            <span class="text-2xl text-[#ff2e63] tracking-tighter">{{ formatCurrency(total) }}</span>
                        </div>
                        <button 
                            @click="submitInvoice"
                            :disabled="cart.length === 0 || isProcessing || !customer_id"
                            class="w-full py-4 bg-[#ff2e63] text-white rounded-[16px] font-black uppercase tracking-[0.2em] text-[11px] shadow-xl shadow-[#ff2e63]/20 hover:bg-[#e60045] transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center gap-3"
                        >
                            <span v-if="!isProcessing">{{ cartStore.language === 'ar' ? 'تأكيد العملية' : 'Confirm Order' }}</span>
                            <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Modal -->
        <div v-if="showReceiptModal" class="fixed inset-0 z-[200] flex items-center justify-center p-8 bg-[#1f1f1f]/80 backdrop-blur-sm" @click.self="showReceiptModal = false">
            <div id="receipt-modal" class="bg-white w-full max-w-lg rounded-[48px] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-500 flex flex-col items-center p-12 text-center border border-[#ececf2]">
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-8 border border-emerald-100/50 shadow-inner">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                </div>
                <h3 class="text-3xl font-black text-[#1f1f1f] tracking-tighter mb-2">{{ cartStore.language === 'ar' ? 'تمت العملية بنجاح' : 'Sale Completed' }}</h3>
                <p class="text-[11px] font-black uppercase tracking-widest text-[#ff2e63] mb-10">{{ lastCompletedInvoice?.invoice?.invoice_number }}</p>
                
                <div id="receipt-area" class="w-full bg-[#f8f9fb] rounded-[32px] p-8 space-y-4 mb-10 border border-[#ececf2] text-left">
                    <div class="flex justify-between items-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <span>Paid via {{ lastCompletedInvoice?.order?.payment_method?.toUpperCase() }}</span>
                        <span>{{ new Date(lastCompletedInvoice?.order?.created_at).toLocaleDateString() }}</span>
                    </div>
                    <div class="flex justify-between items-center text-2xl font-black text-[#1f1f1f] tracking-tighter">
                        <span>Total Paid</span>
                        <span>{{ formatCurrency(lastCompletedInvoice?.invoice?.amount_paid) }}</span>
                    </div>
                    <div class="border-t border-dashed border-[#ececf2] pt-4 mt-4">
                        <p class="text-[10px] font-black text-gray-400 uppercase text-center tracking-[0.2em]">Thank you for your business</p>
                    </div>
                </div>

                <div class="flex flex-col w-full gap-4">
                    <button @click="printReceipt" class="w-full py-5 bg-[#1f1f1f] text-white rounded-[24px] font-black uppercase tracking-widest text-[11px] hover:bg-black transition-all active:scale-95 shadow-xl">
                        Print Invoice
                    </button>
                    <button @click="showReceiptModal = false" class="w-full py-5 bg-white text-[#1f1f1f] border border-[#ececf2] rounded-[24px] font-black uppercase tracking-widest text-[11px] hover:bg-gray-50 transition-all">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Customer Induction Modal -->
        <div v-if="showCustomerModal" class="fixed inset-0 z-[120] flex items-center justify-center p-8 bg-[#1f1f1f]/40 backdrop-blur-md" @click.self="showCustomerModal = false">
            <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-500 border border-[#ececf2]">
                <div class="p-10">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-3xl font-black text-[#1f1f1f] tracking-tighter">{{ cartStore.language === 'ar' ? 'عميل جديد' : 'New Customer' }}</h3>
                        <button @click="showCustomerModal = false" class="p-2 text-gray-400 hover:text-[#ff2e63] transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <form @submit.prevent="createCustomer" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-gray-400 px-2">Full Name</label>
                            <input v-model="customerForm.name" type="text" class="w-full bg-[#f8f9fb] border-[#ececf2] rounded-[20px] py-5 px-8 text-sm font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] text-[#1f1f1f]" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-gray-400 px-2">Phone Number</label>
                            <input v-model="customerForm.phone" type="text" class="w-full bg-[#f8f9fb] border-[#ececf2] rounded-[20px] py-5 px-8 text-sm font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] text-[#1f1f1f]" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[11px] font-black uppercase text-gray-400 px-2">Email (Optional)</label>
                            <input v-model="customerForm.email" type="email" class="w-full bg-[#f8f9fb] border-[#ececf2] rounded-[20px] py-5 px-8 text-sm font-bold focus:ring-[#ff2e63]/20 focus:border-[#ff2e63] text-[#1f1f1f]">
                        </div>
                        <button type="submit" class="w-full py-6 bg-[#ff2e63] text-white rounded-[24px] font-black uppercase tracking-widest text-[11px] shadow-2xl shadow-[#ff2e63]/20 hover:bg-[#e60045] transition-all mt-4">
                            Save & Select Customer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History Modal -->
        <div v-if="showHistoryModal" class="fixed inset-0 z-[150] flex items-center justify-center p-8 bg-[#1f1f1f]/40 backdrop-blur-md" @click.self="showHistoryModal = false">
            <div class="bg-white w-full max-w-3xl rounded-[40px] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-500 h-[80vh] flex flex-col border border-[#ececf2]">
                <div class="p-12 border-b border-[#f8f9fb] flex items-center justify-between">
                    <div>
                        <h3 class="text-3xl font-black text-[#1f1f1f] tracking-tighter uppercase">{{ fullHistory?.customer.name }}</h3>
                        <p class="text-[10px] font-black uppercase tracking-widest text-[#ff2e63] mt-2">Purchase History Ledger</p>
                    </div>
                    <button @click="showHistoryModal = false" class="p-4 bg-[#f8f9fb] text-gray-400 rounded-full hover:text-[#ff2e63] transition-all">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-12 space-y-6 custom-scrollbar bg-[#f8f9fb]/30">
                    <div v-if="fullHistory?.invoices.length === 0" class="text-center py-32 text-gray-300 font-black uppercase text-[10px] tracking-widest">No transaction history</div>
                    <div v-for="inv in fullHistory?.invoices" :key="inv.id" class="bg-white p-6 rounded-[28px] border border-[#ececf2] flex items-center justify-between hover:border-[#ff2e63] hover:shadow-xl hover:shadow-[#ff2e63]/5 transition-all">
                        <div :class="cartStore.language === 'ar' ? 'text-right' : 'text-left'">
                            <p class="text-sm font-bold text-[#1f1f1f]">{{ inv.invoice_number }}</p>
                            <p class="text-[10px] font-black text-gray-400 mt-2 uppercase">{{ inv.created_at }}</p>
                        </div>
                        <div class="text-right flex flex-col items-end gap-3">
                            <p class="text-lg font-black text-[#1f1f1f] tracking-tighter">{{ formatCurrency(inv.total) }}</p>
                            <span class="text-[8px] font-black uppercase tracking-widest px-3 py-1 rounded-lg border" :class="inv.status === 'paid' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-red-50 text-red-600 border-red-100'">{{ inv.status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #ececf2;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #d1d1d1;
}

@media print {
    body * {
        visibility: hidden;
    }
    #receipt-area, #receipt-area * {
        visibility: visible;
    }
    #receipt-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
