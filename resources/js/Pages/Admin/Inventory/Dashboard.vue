<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { cart } from '@/Stores/CartStore';
import { computed } from 'vue';

const props = defineProps({
    metrics: Object,
    lowStockProducts: Array,
    topSelling: Array,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat(cart.language === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
    }).format(value);
};
</script>

<template>
    <Head :title="cart.language === 'ar' ? 'لوحة تحكم المخزون والأرباح' : 'Inventory & Profit Command Hub'" />

    <AdminLayout>
        <template #header>{{ cart.language === 'ar' ? 'الأداء المالي وحالة المخزون الاستراتيجي' : 'Structural Inventory & Financial Performance' }}</template>

        <div class="space-y-10 font-inter" :dir="cart.language === 'ar' ? 'rtl' : 'ltr'">
            <!-- Primary Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stock Valuation -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 opacity-[0.03] group-hover:scale-110 transition-transform">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">{{ cart.language === 'ar' ? 'قيمة المخزون (بالتكلفة)' : 'Net Stock Valuation' }}</h4>
                    <div class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatCurrency(metrics.total_stock_value) }}</div>
                    <div class="mt-4 flex items-center gap-2 text-[9px] font-black text-emerald-600 uppercase">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        {{ cart.language === 'ar' ? 'أصول تشغيلية نشطة' : 'Active Liquid Assets' }}
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden group">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">{{ cart.language === 'ar' ? 'إجمالي المبيعات' : 'Gross Strategic Revenue' }}</h4>
                    <div class="text-3xl font-black text-gray-900 tracking-tighter">{{ formatCurrency(metrics.total_revenue) }}</div>
                    <div class="mt-4 flex items-center gap-2 text-[9px] font-black text-gray-500 uppercase">
                        {{ cart.language === 'ar' ? 'الناتج الإجمالي المحقق' : 'Total Revenue Flux' }}
                    </div>
                </div>

                <!-- Strategic Profit -->
                <div class="bg-gray-900 p-8 rounded-[32px] shadow-2xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-8 text-white/5 group-hover:scale-110 transition-transform">
                        <svg class="w-20 h-20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h4 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-4">{{ cart.language === 'ar' ? 'صافي الربح الحقيقي' : 'Net Strategic Profit' }}</h4>
                    <div class="text-3xl font-black text-white tracking-tighter">{{ formatCurrency(metrics.total_profit) }}</div>
                    <div class="mt-4 flex items-center gap-2 text-[10px] font-black text-emerald-400 uppercase">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                        {{ cart.language === 'ar' ? 'عائد استثماري إيجابي' : 'Positive ROI Detected' }}
                    </div>
                </div>

                <!-- Loss Monitor -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden group">
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">{{ cart.language === 'ar' ? 'إجمالي الخسائر / الخصم' : 'Loss / Margin Decay' }}</h4>
                    <div class="text-3xl font-black tracking-tighter" :class="metrics.total_loss > 0 ? 'text-red-600' : 'text-gray-900'">{{ formatCurrency(metrics.total_loss) }}</div>
                    <div class="mt-4 text-[9px] font-black uppercase" :class="metrics.total_loss > 0 ? 'text-red-400' : 'text-gray-400'">
                        {{ cart.language === 'ar' ? 'بيع تحت التكلفة أو خصومات' : 'Margin Erosion Monitor' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Performers -->
                <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-black text-gray-900 tracking-tighter uppercase mb-8">{{ cart.language === 'ar' ? 'المنتجات الأكثر ربحية' : 'High-Yield Assets' }}</h3>
                    <div class="space-y-6">
                        <div v-for="item in topSelling" :key="item.product_id" class="flex items-center justify-between p-6 bg-gray-50 rounded-[20px] hover:bg-white hover:shadow-lg transition-all border border-transparent hover:border-gray-100 group">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-white rounded-[14px] flex items-center justify-center font-black text-sm text-gray-400 border border-gray-100 group-hover:rotate-6 transition-transform">#</div>
                                <div>
                                    <div class="font-black text-gray-900 text-sm">{{ item.product?.name }}</div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ item.total_sold }} {{ cart.language === 'ar' ? 'وحدة مباعة' : 'Units Sold' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-black text-gray-900">{{ formatCurrency(item.total_profit) }}</div>
                                <div class="text-[9px] font-black text-emerald-500 uppercase tracking-tighter">{{ cart.language === 'ar' ? 'مساهمة ربحية' : 'Yield Contribution' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Risk Factor -->
                <div class="bg-white p-10 rounded-[32px] border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-xl font-black text-gray-900 tracking-tighter uppercase">{{ cart.language === 'ar' ? 'تنبيهات انخفاض المخزون' : 'Stock Critical Depletion' }}</h3>
                        <span class="bg-red-50 text-red-600 px-4 py-2 rounded-full text-[9px] font-black uppercase animate-pulse">{{ cart.language === 'ar' ? 'حالة حرجة' : 'Immediate Red' }}</span>
                    </div>
                    <div class="space-y-4">
                        <div v-for="p in lowStockProducts" :key="p.id" class="flex items-center justify-between p-6 border border-gray-50 rounded-[20px] transition-all hover:bg-red-50/30">
                            <div class="flex items-center gap-5">
                                <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                </div>
                                <div>
                                    <div class="font-black text-gray-900 text-sm">{{ p.name }}</div>
                                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ cart.language === 'ar' ? 'المستوى الحرج:' : 'Alert Threshold:' }} {{ p.min_stock_alert }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-black text-red-600 tracking-tighter">{{ p.stock_quantity }}</div>
                                <div class="text-[10px] font-black text-gray-400 uppercase">{{ cart.language === 'ar' ? 'متبقي حالياً' : 'Remaining' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link :href="route('admin.purchases.index')" class="bg-white p-8 rounded-[28px] border border-gray-100 flex items-center gap-6 hover:shadow-xl hover:border-gray-900 transition-all group">
                    <div class="w-14 h-14 bg-gray-50 rounded-[20px] flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    </div>
                    <div>
                        <div class="font-black text-gray-900 text-sm tracking-tight">{{ cart.language === 'ar' ? 'إدخال شحنة واردة' : 'Induct New Shipment' }}</div>
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ cart.language === 'ar' ? 'تسجيل فواتير شراء' : 'Register External Supply' }}</div>
                    </div>
                </Link>
                <Link :href="route('admin.inventory.reports.profit')" class="bg-white p-8 rounded-[28px] border border-gray-100 flex items-center gap-6 hover:shadow-xl hover:border-gray-900 transition-all group">
                    <div class="w-14 h-14 bg-gray-50 rounded-[20px] flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    </div>
                    <div>
                        <div class="font-black text-gray-900 text-sm tracking-tight">{{ cart.language === 'ar' ? 'تقارير الأرباح التفصيلية' : 'Full Profit Analytics' }}</div>
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ cart.language === 'ar' ? 'تحليل العوائد والمركز المالي' : 'Financial Yield Assessment' }}</div>
                    </div>
                </Link>
                <Link :href="route('admin.inventory.reports.valuation')" class="bg-white p-8 rounded-[28px] border border-gray-100 flex items-center gap-6 hover:shadow-xl hover:border-gray-900 transition-all group">
                    <div class="w-14 h-14 bg-gray-50 rounded-[20px] flex items-center justify-center text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </div>
                    <div>
                        <div class="font-black text-gray-900 text-sm tracking-tight">{{ cart.language === 'ar' ? 'جرد وقيمة المخازن' : 'Structural Inventory Audit' }}</div>
                        <div class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">{{ cart.language === 'ar' ? 'تقييم الأصول المادية' : 'Physical Asset Valuation' }}</div>
                    </div>
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>
