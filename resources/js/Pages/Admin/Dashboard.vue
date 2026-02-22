<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import { cart } from "@/Stores/CartStore";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";
import { Line } from "vue-chartjs";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
);

const props = defineProps({
    stats: Object,
    chartData: Array,
    topProducts: Array,
    worstProducts: Array,
    topCustomers: Array,
    mostActiveCustomer: Object,
    categories: Array,
    recentInvoices: Array,
    lowStockList: Array,
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat(cart.language === "ar" ? "ar-EG" : "en-US", {
        style: "currency",
        currency: "EGP",
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatNumber = (val) => {
    return new Intl.NumberFormat(
        cart.language === "ar" ? "ar-EG" : "en-US",
    ).format(val || 0);
};

// --- Chart Configuration ---
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: "index", intersect: false },
    plugins: {
        legend: { display: false },
        tooltip: {
            backgroundColor: "rgba(255, 255, 255, 0.9)",
            titleColor: "#111",
            bodyColor: "#666",
            borderColor: "#f2f2f2",
            borderWidth: 1,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
        },
    },
    scales: {
        x: {
            grid: { display: false },
            ticks: { font: { size: 10, family: "Inter" }, color: "#a3a3a3" },
        },
        y: {
            grid: { color: "#f2f2f2", borderDash: [5, 5] },
            border: { display: false },
            ticks: { font: { size: 10, family: "Inter" }, color: "#a3a3a3" },
        },
    },
};

const chartDataObj = computed(() => {
    if (!props.chartData || !props.chartData.length)
        return { labels: [], datasets: [] };

    return {
        labels: props.chartData.map((d) =>
            d.date.split("-").slice(1).join("/"),
        ),
        datasets: [
            {
                label: "Sales Revenue",
                data: props.chartData.map((d) => d.sales),
                borderColor: "#111111",
                backgroundColor: "rgba(17, 17, 17, 0.05)",
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 0,
                pointHoverRadius: 6,
            },
            {
                label: "Net Profit",
                data: props.chartData.map((d) => d.profit),
                borderColor: "#00b86b",
                backgroundColor: "transparent",
                borderWidth: 3,
                borderDash: [5, 5],
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
            },
            {
                label: "Expenses",
                data: props.chartData.map((d) => d.expenses),
                borderColor: "#ff2b2b",
                backgroundColor: "transparent",
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
            },
        ],
    };
});

// --- Smart Alerts Logic ---
const alerts = computed(() => {
    const list = [];
    if (props.stats.low_stock_count > 0)
        list.push({
            type: "danger",
            msg: `Critical: ${props.stats.low_stock_count} products are running low on stock.`,
        });
    if (props.stats.net_profit_today < 0)
        list.push({
            type: "danger",
            msg: "Warning: Net profit today is currently negative.",
        });
    if (props.stats.pending_payments > 50000)
        list.push({
            type: "warning",
            msg: `High Pending Receivables: ${formatCurrency(props.stats.pending_payments)}`,
        });
    if (props.stats.total_sales_today > 10000)
        list.push({
            type: "success",
            msg: "Strong performance today! High sales volume detected.",
        });
    return list;
});
</script>

<template>
    <Head
        :title="
            cart.language === 'ar'
                ? 'التحليلات الذكية'
                : 'Intelligence Dashboard'
        "
    />

    <AdminLayout>
        <template #header>{{
            cart.language === "ar"
                ? "منصة ذكاء الأعمال والتحليلات"
                : "Business Intelligence & Analytics Platform"
        }}</template>

        <div
            class="space-y-8 font-inter bg-[#f9fafb] p-6 lg:p-10 -m-12 min-h-screen"
            :dir="cart.language === 'ar' ? 'rtl' : 'ltr'"
        >
            <!-- Quick Actions Bar -->
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1
                    class="text-3xl font-extrabold text-[#111] tracking-tighter"
                >
                    {{ cart.language === "ar" ? "نظرة عامة" : "Overview" }}
                </h1>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.pos.index')"
                        class="px-5 py-2.5 bg-gradient-to-r from-[#ff2b2b] to-[#ff4d4d] text-white text-[11px] font-bold uppercase tracking-widest rounded-[12px] shadow-[0_8px_20px_rgba(255,43,43,0.25)] hover:-translate-y-0.5 transition-all"
                    >
                        + {{ cart.language === "ar" ? "نقطة بيع" : "New Sale" }}
                    </Link>
                    <Link
                        :href="route('admin.expenses.index')"
                        class="px-5 py-2.5 bg-white text-[#111] border border-[#f2f2f2] text-[11px] font-bold uppercase tracking-widest rounded-[12px] shadow-sm hover:-translate-y-0.5 hover:border-[#ff2b2b] transition-all"
                    >
                        +
                        {{
                            cart.language === "ar"
                                ? "تسجيل مصروف"
                                : "Add Expense"
                        }}
                    </Link>
                    <Link
                        :href="route('admin.products.create')"
                        class="px-5 py-2.5 bg-white text-[#111] border border-[#f2f2f2] text-[11px] font-bold uppercase tracking-widest rounded-[12px] shadow-sm hover:-translate-y-0.5 transition-all hidden md:block"
                    >
                        +
                        {{
                            cart.language === "ar" ? "منتج جديد" : "New Product"
                        }}
                    </Link>
                </div>
            </div>

            <!-- Smart Alerts -->
            <div v-if="alerts.length > 0" class="flex flex-col gap-2">
                <div
                    v-for="(alert, i) in alerts.slice(0, 2)"
                    :key="i"
                    class="px-5 py-4 rounded-[14px] flex items-center gap-3 font-semibold text-sm shadow-sm border"
                    :class="
                        alert.type === 'danger'
                            ? 'bg-[#fff4f4] text-[#ff2b2b] border-[#ffe0e0]'
                            : alert.type === 'success'
                              ? 'bg-[#f0fdf4] text-[#00b86b] border-[#dcfce7]'
                              : 'bg-[#fffbeb] text-[#f59e0b] border-[#fef3c7]'
                    "
                >
                    <span class="text-lg">{{
                        alert.type === "danger"
                            ? "⚠️"
                            : alert.type === "success"
                              ? "🚀"
                              : "⚡"
                    }}</span>
                    {{ alert.msg }}
                </div>
            </div>

            <!-- 1. CORE FINANCIAL ENGINE -->
            <div>
                <h3
                    class="text-[11px] font-black uppercase tracking-[0.2em] text-[#a3a3a3] mb-4"
                >
                    {{
                        cart.language === "ar"
                            ? "المؤشرات المالية الحية"
                            : "Core Financial Engine"
                    }}
                </h3>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"
                >
                    <div
                        class="bg-white rounded-[18px] p-6 border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:-translate-y-1 transition-transform"
                    >
                        <p
                            class="text-[11px] font-bold uppercase tracking-widest text-[#666] mb-3"
                        >
                            {{
                                cart.language === "ar"
                                    ? "إيرادات اليوم (مدفوع)"
                                    : "Today Revenue (Paid)"
                            }}
                        </p>
                        <h2
                            class="text-3xl font-extrabold text-[#111] tracking-tighter"
                        >
                            {{ formatCurrency(stats.total_sales_today) }}
                        </h2>
                        <span
                            class="inline-block mt-3 text-[10px] font-bold px-2 py-1 bg-[#f9fafb] text-[#666] border border-[#f2f2f2] rounded-lg"
                            >Orders: {{ stats.orders_today }}</span
                        >
                    </div>

                    <div
                        class="bg-white rounded-[18px] p-6 border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:-translate-y-1 transition-transform relative overflow-hidden"
                    >
                        <div
                            class="absolute right-0 top-0 w-1.5 h-full"
                            :class="
                                stats.net_profit_today >= 0
                                    ? 'bg-[#00b86b]'
                                    : 'bg-[#ff2b2b]'
                            "
                        ></div>
                        <p
                            class="text-[11px] font-bold uppercase tracking-widest text-[#666] mb-3"
                        >
                            {{
                                cart.language === "ar"
                                    ? "صافي ربح اليوم"
                                    : "Net Profit Today"
                            }}
                        </p>
                        <h2
                            class="text-3xl font-extrabold tracking-tighter"
                            :class="
                                stats.net_profit_today >= 0
                                    ? 'text-[#00b86b]'
                                    : 'text-[#ff2b2b]'
                            "
                        >
                            {{ formatCurrency(stats.net_profit_today) }}
                        </h2>
                        <span
                            class="inline-block mt-3 text-[10px] font-bold px-2 py-1 bg-[#f9fafb] text-[#666] border border-[#f2f2f2] rounded-lg"
                            >Sales - Cost - Expenses</span
                        >
                    </div>

                    <div
                        class="bg-white rounded-[18px] p-6 border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:-translate-y-1 transition-transform"
                    >
                        <p
                            class="text-[11px] font-bold uppercase tracking-widest text-[#666] mb-3"
                        >
                            {{
                                cart.language === "ar"
                                    ? "النقد في الصندوق اليوم"
                                    : "Cash In Hand Today"
                            }}
                        </p>
                        <h2
                            class="text-3xl font-extrabold text-[#111] tracking-tighter"
                        >
                            {{ formatCurrency(stats.cash_in_hand_today) }}
                        </h2>
                        <span
                            class="inline-block mt-3 text-[10px] font-bold px-2 py-1 bg-[#f9fafb] text-[#666] border border-[#f2f2f2] rounded-lg"
                            >Collected Net</span
                        >
                    </div>

                    <div
                        class="bg-white rounded-[18px] p-6 border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.03)] hover:-translate-y-1 transition-transform"
                    >
                        <p
                            class="text-[11px] font-bold uppercase tracking-widest text-[#666] mb-3"
                        >
                            {{
                                cart.language === "ar"
                                    ? "إجمالي مصروفات الشهر"
                                    : "Total Month Expenses"
                            }}
                        </p>
                        <h2
                            class="text-3xl font-extrabold text-[#ff2b2b] tracking-tighter"
                        >
                            {{ formatCurrency(stats.total_expenses_month) }}
                        </h2>
                        <span
                            class="inline-block mt-3 text-[10px] font-bold px-2 py-1 bg-[#fff4f4] text-[#ff2b2b] border border-[#ffe0e0] rounded-lg"
                            >- Outflow</span
                        >
                    </div>
                </div>
            </div>

            <!-- Main Chart Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Analytics Chart -->
                <div
                    class="lg:col-span-2 bg-white rounded-[20px] p-8 border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.02)]"
                >
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3
                                class="text-lg font-extrabold text-[#111] tracking-tight"
                            >
                                {{
                                    cart.language === "ar"
                                        ? "الأداء المالي (14 يوم)"
                                        : "Financial Velocity (14 Days)"
                                }}
                            </h3>
                            <p
                                class="text-[11px] text-[#a3a3a3] mt-1 font-semibold"
                            >
                                {{
                                    cart.language === "ar"
                                        ? "مقارنة المبيعات، المصروفات وصافي الأرباح المستهدفة"
                                        : "Sales vs Expenses vs Profit margins"
                                }}
                            </p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-3 h-3 rounded-full bg-[#111]"
                                ></span
                                ><span
                                    class="text-[10px] font-bold text-[#666] uppercase tracking-widest"
                                    >Sales</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-3 h-3 rounded-full bg-[#00b86b]"
                                ></span
                                ><span
                                    class="text-[10px] font-bold text-[#666] uppercase tracking-widest"
                                    >Profit</span
                                >
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="w-3 h-3 rounded-full bg-[#ff2b2b]"
                                ></span
                                ><span
                                    class="text-[10px] font-bold text-[#666] uppercase tracking-widest"
                                    >Expense</span
                                >
                            </div>
                        </div>
                    </div>
                    <div class="h-[300px] w-full">
                        <Line
                            v-if="chartDataObj.labels.length"
                            :data="chartDataObj"
                            :options="chartOptions"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center text-gray-300 font-bold text-sm"
                        >
                            Waiting for metric ingestion...
                        </div>
                    </div>
                </div>

                <!-- 2. INVENTORY INTELLIGENCE -->
                <div
                    class="bg-gradient-to-br from-[#111111] to-[#1a1a1a] rounded-[20px] p-8 shadow-[0_15px_35px_rgba(0,0,0,0.15)] flex flex-col justify-between relative overflow-hidden"
                >
                    <!-- Deco -->
                    <div
                        class="absolute -right-8 -top-8 w-32 h-32 bg-[#222] rounded-full blur-2xl"
                    ></div>

                    <div>
                        <h3
                            class="text-[11px] font-black uppercase tracking-[0.2em] text-[#888] mb-6"
                        >
                            {{
                                cart.language === "ar"
                                    ? "ذكاء المخزون"
                                    : "Inventory Intelligence"
                            }}
                        </h3>

                        <div class="space-y-6 relative z-10">
                            <div>
                                <p class="text-xs text-[#888] font-bold mb-1">
                                    {{
                                        cart.language === "ar"
                                            ? "القيمة الإجمالية للمخزون (تكلفة)"
                                            : "Stock Value (Cost Basis)"
                                    }}
                                </p>
                                <h4
                                    class="text-2xl font-black text-white tracking-tighter"
                                >
                                    {{ formatCurrency(stats.stock_value_cost) }}
                                </h4>
                            </div>
                            <div>
                                <p class="text-xs text-[#888] font-bold mb-1">
                                    {{
                                        cart.language === "ar"
                                            ? "الربح المتوقع (مستودع)"
                                            : "Expected Profit in Vault"
                                    }}
                                </p>
                                <h4
                                    class="text-2xl font-black text-[#00b86b] tracking-tighter"
                                >
                                    {{
                                        formatCurrency(
                                            stats.expected_inventory_profit,
                                        )
                                    }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-8 pt-6 border-t border-[#333] grid grid-cols-2 gap-4 relative z-10"
                    >
                        <div>
                            <p
                                class="text-[10px] text-[#666] uppercase tracking-widest font-bold mb-1"
                            >
                                Low Stock
                            </p>
                            <span class="text-lg font-black text-[#f59e0b]"
                                >{{ stats.low_stock_count }} items</span
                            >
                        </div>
                        <div>
                            <p
                                class="text-[10px] text-[#666] uppercase tracking-widest font-bold mb-1"
                            >
                                Out of Stock
                            </p>
                            <span class="text-lg font-black text-[#ff2b2b]"
                                >{{ stats.out_of_stock_count }} items</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insight Box (God Mode) -->
            <div
                class="bg-white rounded-[16px] border border-[#f2f2f2] p-6 shadow-[0_10px_30px_rgba(0,0,0,0.02)] flex items-start gap-4"
            >
                <div
                    class="w-10 h-10 rounded-full bg-gradient-to-br from-[#ff2b2b] to-[#ff4d4d] flex items-center justify-center text-white shrink-0 shadow-inner"
                >
                    <svg
                        class="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                    </svg>
                </div>
                <div>
                    <h4
                        class="text-xs font-black uppercase tracking-widest text-[#111] mb-1"
                    >
                        System Intelligence
                    </h4>
                    <p class="text-sm text-[#666] font-medium leading-relaxed">
                        <span v-if="stats.net_profit_today > 0"
                            >Profit generation is active today.
                        </span>
                        <span
                            v-if="stats.out_of_stock_count > 0"
                            class="text-[#ff2b2b] font-bold"
                            >Action Required:
                            {{ stats.out_of_stock_count }} SKUs are completely
                            depleted.
                        </span>
                        Top performing product right now is
                        <strong class="text-[#111]">{{
                            topProducts[0]?.product?.name || "N/A"
                        }}</strong
                        >. We have accumulated
                        <strong class="text-[#111]">{{
                            formatCurrency(stats.pending_payments)
                        }}</strong>
                        in pending receivables.
                    </p>
                </div>
            </div>

            <!-- Deep Analytics Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Top Products -->
                <div
                    class="bg-white rounded-[18px] border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.02)] overflow-hidden"
                >
                    <div class="p-6 border-b border-[#f9fafb]">
                        <h4
                            class="text-sm font-extrabold text-[#111] uppercase tracking-widest"
                        >
                            {{
                                cart.language === "ar"
                                    ? "أفضل المبيعات"
                                    : "Top Selling SKUs"
                            }}
                        </h4>
                    </div>
                    <div class="divide-y divide-[#f9fafb]">
                        <div
                            v-for="(item, idx) in topProducts"
                            :key="idx"
                            class="p-4 flex items-center justify-between hover:bg-[#fcfcfc] transition-colors"
                        >
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-xs font-black text-[#ccc] w-4"
                                    >{{ idx + 1 }}</span
                                >
                                <span
                                    class="text-sm font-bold text-[#111] truncate max-w-[150px]"
                                    >{{ item.product?.name || "Unknown" }}</span
                                >
                            </div>
                            <span
                                class="text-xs font-black text-[#00b86b] bg-[#f0fdf4] px-2.5 py-1 rounded-md"
                                >{{ item.total_sold }} units</span
                            >
                        </div>
                    </div>
                </div>

                <!-- Order Analytics -->
                <div
                    class="bg-white rounded-[18px] border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.02)] overflow-hidden p-6 relative"
                >
                    <h4
                        class="text-sm font-extrabold text-[#111] uppercase tracking-widest mb-6"
                    >
                        {{
                            cart.language === "ar"
                                ? "تحليلات الطلبات الحية"
                                : "Order Analytics"
                        }}
                    </h4>

                    <div class="space-y-6">
                        <div
                            class="flex items-end justify-between border-b border-[#f2f2f2] pb-4"
                        >
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-[#a3a3a3] mb-1"
                                >
                                    Average Order Value
                                </p>
                                <h4
                                    class="text-2xl font-black text-[#111] tracking-tighter"
                                >
                                    {{
                                        formatCurrency(
                                            stats.average_order_value,
                                        )
                                    }}
                                </h4>
                            </div>
                            <div
                                class="w-8 h-8 rounded-full bg-[#f9fafb] flex items-center justify-center text-[#666]"
                            >
                                📈
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-[#a3a3a3] mb-1"
                                >
                                    Month Orders
                                </p>
                                <h4
                                    class="text-xl font-black text-[#111] tracking-tighter"
                                >
                                    {{ formatNumber(stats.orders_month) }}
                                </h4>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-[#a3a3a3] mb-1"
                                >
                                    Month Revenue
                                </p>
                                <h4
                                    class="text-xl font-black text-[#111] tracking-tighter"
                                >
                                    {{ formatCurrency(stats.monthly_revenue) }}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Intelligence -->
                <div
                    class="bg-white rounded-[18px] border border-[#f2f2f2] shadow-[0_10px_30px_rgba(0,0,0,0.02)] overflow-hidden"
                >
                    <div class="p-6 border-b border-[#f9fafb]">
                        <h4
                            class="text-sm font-extrabold text-[#111] uppercase tracking-widest"
                        >
                            {{
                                cart.language === "ar"
                                    ? "شبكة العملاء"
                                    : "Customer Network"
                            }}
                        </h4>
                    </div>
                    <div class="p-6 space-y-6">
                        <div v-if="mostActiveCustomer">
                            <p
                                class="text-[10px] font-bold uppercase tracking-widest text-[#ff2b2b] mb-1"
                            >
                                Most Active Champion
                            </p>
                            <h4
                                class="text-lg font-black text-[#111] tracking-tight"
                            >
                                {{
                                    mostActiveCustomer.details?.name ||
                                    "Walk-in"
                                }}
                            </h4>
                            <p class="text-xs text-[#888] font-semibold mt-1">
                                LTV:
                                {{
                                    formatCurrency(
                                        mostActiveCustomer.total_spent,
                                    )
                                }}
                                • {{ mostActiveCustomer.orders_count }} Orders
                            </p>
                        </div>

                        <div
                            class="pt-4 border-t border-[#f2f2f2] grid grid-cols-2 gap-4"
                        >
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-[#a3a3a3] mb-1"
                                >
                                    New this month
                                </p>
                                <h4
                                    class="text-lg font-black text-[#111] tracking-tighter"
                                >
                                    +{{ stats.new_customers_month }}
                                </h4>
                            </div>
                            <div>
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest text-[#a3a3a3] mb-1"
                                >
                                    In Debt
                                </p>
                                <h4
                                    class="text-lg font-black text-[#ff2b2b] tracking-tighter"
                                >
                                    {{
                                        stats.customers_owe_money_count
                                    }}
                                    clients
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
