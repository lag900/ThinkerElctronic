<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { computed } from 'vue';

const props = defineProps({
    revenueData: Array,
    ordersCountData: Array,
    topProducts: Array,
    customerAcquisition: Array,
    categorySales: Array,
    stats: Object,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', { 
        style: 'currency', 
        currency: 'EGP',
        maximumFractionDigits: 0
    }).format(val || 0);
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US').format(val || 0);
};

// Strategic Performance Projection Logic
const maxRevenue = computed(() => Math.max(...props.revenueData.map(d => d.revenue), 1));
const chartPath = computed(() => {
    if (!props.revenueData.length) return '';
    const points = props.revenueData.map((d, i) => {
        const x = (i / (props.revenueData.length - 1)) * 100;
        const y = 100 - (d.revenue / maxRevenue.value) * 100;
        return `${x},${y}`;
    });
    return `M ${points.join(' L ')}`;
});

</script>

<template>
    <Head :title="cart.language === 'ar' ? 'تحليلات الأداء' : 'Operational Analytics'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'التحليلات والتقارير الإستراتيجية' : 'Strategic Business Intelligence' }}</template>

        <div class="space-y-12 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Operational Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="(val, labelAr, labelEn) in {
                    'إجمالي العوائد المالية': [formatCurrency(stats.total_revenue), 'Total Revenue Output'],
                    'إجمالي عدد العمليات': [formatNumber(stats.total_orders), 'Aggregated Order Volume'],
                    'قاعدة العملاء النشطة': [formatNumber(stats.total_customers), 'Active Demographic Hub'],
                    'متوسط قيمة الفاتورة': [formatCurrency(stats.avg_order_value), 'Mean Order Value']
                }" :key="labelAr" class="bg-white p-10 rounded-[20px] border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-2xl hover:shadow-gray-900/5 transition-all">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-900 shadow-inner rounded-bl-[64px] opacity-0 group-hover:opacity-[0.03] transition-all duration-700"></div>
                    <p class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-400 relative z-10">{{ cart.language === 'ar' ? labelAr : labelEn[1] }}</p>
                    <h4 class="text-3xl font-black text-gray-900 tracking-tighter mt-3 relative z-10">{{ val[0] }}</h4>
                </div>
            </div>

            <!-- Strategic Data Projection -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                <!-- Revenue Flux Visualizer -->
                <div class="lg:col-span-2 bg-white p-12 rounded-[24px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-2 bg-gray-900 opacity-5"></div>
                    <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-6">
                        <div :class="cart.language === 'ar' ? 'text-right' : 'text-left'">
                            <h3 class="text-2xl font-black tracking-tighter text-gray-900">{{ cart.language === 'ar' ? 'أداء العوائد المالية' : 'Revenue Performance Flux' }}</h3>
                            <p class="text-xs font-bold text-gray-400 mt-1">{{ cart.language === 'ar' ? 'اتجاهات النمو المحققة خلال آخر 30 يوماً من النشاط' : 'Strategic growth trends over the 30-day operational cycle' }}</p>
                        </div>
                        <div class="flex items-center gap-3 bg-gray-50 px-5 py-2.5 rounded-full border border-gray-100">
                            <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                            <span class="text-[10px] font-black text-gray-600 uppercase tracking-widest">{{ cart.language === 'ar' ? 'تتبع مباشر للنظام' : 'Live Ecosystem Feed' }}</span>
                        </div>
                    </div>
                    
                    <div class="h-72 w-full relative group mt-12">
                        <svg class="w-full h-full overflow-visible" preserveAspectRatio="none" viewBox="0 0 100 100">
                            <!-- Tactical Gradient -->
                            <defs>
                                <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#111827" stop-opacity="0.15" />
                                    <stop offset="100%" stop-color="#111827" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            
                            <!-- Area Projection -->
                            <path :d="chartPath + ' L 100,100 L 0,100 Z'" fill="url(#chartGradient)" class="animate-in fade-in duration-1000" />
                            
                            <!-- Vector Path -->
                            <path :d="chartPath" fill="none" stroke="#111827" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="animate-in slide-in-from-left duration-1000" />
                            
                            <!-- Baseline -->
                            <line x1="0" y1="100" x2="100" y2="100" stroke="#F3F4F6" stroke-width="2" />
                        </svg>
                    </div>

                    <div class="flex justify-between mt-10">
                        <span v-for="d in revenueData.filter((_, i) => i % 5 === 0)" :key="d.date" class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
                            {{ new Date(d.date).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US', { month: 'short', day: 'numeric' }) }}
                        </span>
                    </div>
                </div>

                <!-- Product Authority Ranking -->
                <div class="bg-gray-900 p-12 rounded-[28px] shadow-2xl relative overflow-hidden group">
                    <div class="absolute -top-24 -right-24 w-80 h-80 bg-white/5 rounded-full blur-[80px] group-hover:bg-white/10 transition-all duration-1000"></div>
                    
                    <h3 class="text-2xl font-black tracking-tighter text-white relative z-10">{{ cart.language === 'ar' ? 'الأصناف الأعلى طلباً' : 'Market Force Leaders' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-1 relative z-10">{{ cart.language === 'ar' ? 'ترتيب المنتجات الأكثر مبيعاً في السوق' : 'Top performing assets ranked by acquisition volume' }}</p>

                    <div class="mt-12 space-y-10 relative z-10">
                        <div v-for="p in topProducts" :key="p.name" class="group/item">
                            <div class="flex justify-between items-end mb-4">
                                <span class="text-sm font-black text-white/90 truncate max-w-[180px] tracking-tight">{{ p.name }}</span>
                                <span class="text-[11px] font-black text-gray-500 uppercase tracking-widest bg-white/5 px-2.5 py-1 rounded-md">{{ p.total_sold }} {{ cart.language === 'ar' ? 'مباع' : 'Units' }}</span>
                            </div>
                            <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden border border-white/5 shadow-inner">
                                <div 
                                    class="h-full bg-white rounded-full transition-all duration-1000 delay-300 shadow-[0_0_15px_rgba(255,255,255,0.3)]" 
                                    :style="{ width: `${(p.total_sold / topProducts[0].total_sold) * 100}%` }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <button @click="$inertia.visit(route('admin.inventory.index'))" class="w-full mt-12 py-5 bg-white/5 border border-white/10 hover:bg-white text-white hover:text-gray-900 rounded-[16px] text-[11px] font-black uppercase tracking-[0.3em] transition-all shadow-2xl active:scale-95">
                        {{ cart.language === 'ar' ? 'تحليل حركة المخزون' : 'Deep Inventory Audit' }}
                    </button>
                </div>
            </div>

            <!-- Secondary Tactical Nodes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Demographic Expansion -->
                <div class="bg-white p-12 rounded-[24px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1.5 h-full bg-emerald-500 opacity-20"></div>
                    <h3 class="text-2xl font-black tracking-tighter text-gray-900">{{ cart.language === 'ar' ? 'نمو القاعدة الجماهيرية' : 'User Force Expansion' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-1">{{ cart.language === 'ar' ? 'مؤشرات استقطاب وتحويل العملاء الجدد' : 'Metrics for new user induction and acquisition flux' }}</p>

                    <div class="mt-12 space-y-5">
                        <div v-for="d in customerAcquisition.slice(-5).reverse()" :key="d.date" class="flex items-center justify-between p-6 bg-gray-50 rounded-[16px] hover:bg-white border border-transparent hover:border-gray-100 transition-all shadow-sm group">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center text-gray-900 font-black shadow-lg border border-gray-50 group-hover:scale-110 transition-transform">
                                    +{{ d.count }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-900 uppercase tracking-widest">{{ cart.language === 'ar' ? 'عميل جديد تم استقطابه' : 'NEW ASSET INDUCTED' }}</p>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">{{ new Date(d.date).toLocaleDateString(cart.language === 'ar' ? 'ar-EG' : 'en-US', { weekday: 'long', month: 'short', day: 'numeric' }) }}</p>
                                </div>
                            </div>
                            <div class="w-10 h-10 bg-emerald-50 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Structural Distribution -->
                <div class="bg-white p-12 rounded-[24px] border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-1.5 h-full bg-gray-900 opacity-5"></div>
                    <h3 class="text-2xl font-black tracking-tighter text-gray-900">{{ cart.language === 'ar' ? 'توزيع المبيعات حسب التصنيف' : 'Taxonomy Sales Distribution' }}</h3>
                    <p class="text-xs font-bold text-gray-400 mt-1">{{ cart.language === 'ar' ? 'تركيز العمليات الشرائية على خطوط الإنتاج الهيكلية' : 'Operational concentration across structural taxonomy nodes' }}</p>

                    <div class="mt-12 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div v-for="cat in categorySales" :key="cat.name" class="p-10 bg-gray-50 rounded-[20px] flex flex-col items-center justify-center text-center group transition-all hover:bg-gray-900 shadow-inner">
                            <h4 class="text-4xl font-black text-gray-900 group-hover:text-white transition-all tracking-tighter">{{ cat.count }}</h4>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mt-3 group-hover:text-white/40 transition-all">{{ cat.name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
