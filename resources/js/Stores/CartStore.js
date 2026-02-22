import { reactive, computed } from 'vue';

// Check for existing data in storage
const savedCart = typeof window !== 'undefined' ? localStorage.getItem('thinker_cart') : null;
const savedLang = typeof window !== 'undefined' ? (localStorage.getItem('thinker_lang') || 'ar') : 'ar';

// Set initial document state
if (typeof window !== 'undefined') {
    document.documentElement.dir = savedLang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLang;
}

export const cart = reactive({
    items: savedCart ? JSON.parse(savedCart) : [],
    currency: 'EGP',
    language: savedLang,
    
    saveToStorage() {
        if (typeof window !== 'undefined') {
            localStorage.setItem('thinker_cart', JSON.stringify(this.items));
            localStorage.setItem('thinker_lang', this.language);
        }
    },

    addItem(product) {
        const qtyToAdd = product.quantity || 1;
        const existingItem = this.items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity += qtyToAdd;
        } else {
            this.items.push({ ...product, quantity: qtyToAdd });
        }
        this.saveToStorage();
    },
    
    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveToStorage();
    },
    
    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, parseInt(quantity) || 1);
        }
        this.saveToStorage();
    },
    
    clearCart() {
        this.items = [];
        this.saveToStorage();
    },
    
    get total() {
        return this.items.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    },
    
    get count() {
        return this.items.reduce((sum, item) => sum + item.quantity, 0);
    },

    toggleLanguage() {
        this.language = this.language === 'en' ? 'ar' : 'en';
        document.documentElement.dir = this.language === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.lang = this.language;
        this.saveToStorage();
    },

    t(key) {
        const translations = {
            en: {
                home: 'Home',
                shop: 'Shop',
                printing: '3D Printing Service',
                solutions: 'Software Solutions',
                contact: 'Contact',
                addToCart: 'Add to Cart',
                cart: 'Your Cart',
                checkout: 'Checkout',
                emptyCart: 'Your cart is as empty as space.',
                startShopping: 'Start Shopping',
                subtotal: 'Subtotal',
                remove: 'Remove',
                shopNow: 'Shop Now',
                explore: 'Explore Services',
                slogan: 'Smart Thinking. Smart Systems.',
                thinking: 'Thinking.',
                smart: 'Smart',
                innovation: 'Innovation at its core',
                hardware: 'Hardware',
                excellence: 'Excellence.',
                stayAhead: 'Stay Ahead of',
                theFuture: 'The Future.',
                joinNow: 'Join Now',
                ourProducts: 'Our Products',
                discover: 'Discover our latest innovations in smart technology.',
                filter: 'Filter by Price',
                categories: 'Product Categories',
                dashboard: 'Dashboard',
                login: 'Login',
                lmsPointsDesc: 'These points are used in the smart printing system at Borg El Arab Technological University in Alexandria - and to pay for some products displayed on the university\'s platform.',
                fullName: 'Full Name',
                phone: 'Phone number',
                email: 'Email address',
                address: 'Street address',
                city: 'City',
                province: 'Province',
                country: 'Country',
                notes: 'Order notes',
                cod: 'Cash on delivery',
                instapay: 'Instapay transfer',
                creditCard: 'Credit card',
                orderSummary: 'Order summary',
                total: 'Total',
                shipping: 'Shipping',
                placeOrder: 'Place Order',
                paymentMethods: 'Payment Methods',
                shippingDetails: 'Shipping Details',
                profile: 'Profile',
                account: 'Account',
                inventory: 'Inventory',
                analytics: 'Analytics',
                settings: 'Settings',
                viewStore: 'View Store',
                orders: 'Orders',
                customers: 'Customers',
                categories: 'Categories',
                products: 'Products',
                welcome: 'Dashboard',
                salesToday: 'Sales Today',
                revenue: 'Revenue',
                profit: 'Profit',
                performance: 'Performance',
                avgOrder: 'Avg Order',
                noOrders: 'No orders yet.',
                orderId: 'Order ID',
                date: 'Date',
                status: 'Status',
                action: 'Action',
                invoice: 'Invoice',
                lmsPoints: 'LMS Points',
                privacyPolicy: 'Privacy Policy',
                termsOfService: 'Terms of Service',
                refundPolicy: 'Refund Policy',
                contactInfo: 'Contact Info',
                qrSystem: 'QR System',
                pending: 'Pending',
                processing: 'Processing',
                completed: 'Completed',
                failed: 'Failed',
                paid: 'Paid',
                allOrders: 'All Orders',
                findOrder: 'Find order',
                customer: 'Customer',
                value: 'Value',
                createdBy: 'Created By',
                filterByEmployee: 'Filter by Employee',
                invoiceCreatedBy: 'Invoice created by',
                expenses: 'Expenses',
                homeContent: 'Home Content',
                users: 'Users',
                roles: 'Roles',
                selectCustomer: 'Select Customer',
                addCustomer: 'New Customer',
                customerPlaceholder: 'Search by Name, Phone...',
                totalPurchases: 'Total Purchases',
                ordersCount: 'Orders Count',
                lastPurchase: 'Last Purchase',
                totalPaid: 'Total Paid',
                debt: 'Debt',
                vip: 'VIP',
                regular: 'Regular',
                newCustomer: 'New',
                customerName: 'Customer Name',
                phone: 'Phone',
                notes: 'Notes',
                save: 'Save',
                mustSelectCustomer: 'You must select a customer first',
                history: 'Purchase History',
            },
            ar: {
                home: 'الرئيسية',
                shop: 'المتجر',
                printing: 'خدمة الطباعة ثلاثية الأبعاد',
                solutions: 'الحلول البرمجية',
                contact: 'اتصل بنا',
                addToCart: 'إضافة للسلة',
                cart: 'سلتك',
                checkout: 'إتمام الدفع',
                emptyCart: 'سلتك لسه فاضية، مفيش فيها أي منتجات.',
                startShopping: 'ابدأ التسوق',
                subtotal: 'المجموع الفرعى',
                remove: 'حذف',
                shopNow: 'تسوق الآن',
                explore: 'اكتشف خدماتنا',
                slogan: 'تفكير ذكي. أنظمة ذكية.',
                thinking: 'تفكير.',
                smart: 'ذكي',
                innovation: 'الابتكار في جوهرنا',
                hardware: 'الأجهزة والمعدات',
                excellence: 'التميز.',
                stayAhead: 'خليك سابق',
                theFuture: 'المستقبل.',
                joinNow: 'انضم الآن',
                ourProducts: 'منتجاتنا',
                discover: 'اكتشف أحدث ابتكاراتنا في التكنولوجيا الذكية.',
                filter: 'تصفية حسب السعر',
                categories: 'أقسام المنتجات',
                dashboard: 'لوحة التحكم',
                login: 'تسجيل الدخول',
                lmsPointsDesc: 'بيتم استخدام النقاط دي في نظام الطباعة الذكية بجامعة برج العرب التكنولوجية بالإسكندرية - ولدفع تكلفة بعض المنتجات المعروضة على منصة الجامعة.',
                fullName: 'الاسم بالكامل',
                phone: 'رقم الموبايل',
                email: 'البريد الإلكتروني',
                address: 'العنوان بالتفصيل',
                city: 'المدينة',
                province: 'المحافظة',
                country: 'الدولة',
                notes: 'ملاحظات إضافية',
                cod: 'دفع عند الاستلام (كاش)',
                instapay: 'تحويل عبر إنستا باي',
                creditCard: 'دفع بالفيزا / كارت بنكي',
                orderSummary: 'ملخص الطلبية',
                total: 'الإجمالي',
                shipping: 'تكاريف الشحن',
                placeOrder: 'تأكيد الطلب',
                paymentMethods: 'طرق الدفع المتاحة',
                shippingDetails: 'بيانات التوصيل',
                profile: 'الملف الشخصي',
                account: 'حسابي',
                inventory: 'المخزون والعهدة',
                analytics: 'التحليلات والإحصائيات',
                settings: 'إعدادات النظام',
                viewStore: 'زيارة المتجر',
                orders: 'إدارة الطلبات',
                customers: 'قاعدة بيانات العملاء',
                products: 'إدارة المنتجات',
                welcome: 'نظام إدارة تينكر',
                salesToday: 'مبيعات النهاردة',
                revenue: 'إجمالي الإيرادات',
                profit: 'صافي الأرباح',
                performance: 'مؤشرات الأداء',
                avgOrder: 'متوسط قيمة الطلب',
                noOrders: 'لسه مفيش أي طلبات مسجلة.',
                orderId: 'رقم العملية',
                date: 'التاريخ',
                status: 'الحالة',
                action: 'الإجراء',
                invoice: 'الفاتورة الضريبية',
                lmsPoints: 'نقاط LMS',
                privacyPolicy: 'سياسة الخصوصية',
                termsOfService: 'شروط الاستخدام',
                refundPolicy: 'سياسة الاسترجاع',
                contactInfo: 'بيانات التواصل',
                qrSystem: 'نظام QR كود',
                pending: 'قيد الانتظار',
                processing: 'جاري التجهيز',
                completed: 'تم التسليم والتحصيل',
                failed: 'عملية غير مكتملة',
                paid: 'تم الدفع',
                allOrders: 'كافة الطلبات',
                findOrder: 'ابحث عن رقم العملية...',
                customer: 'العميل',
                value: 'القيمة المالية',
                createdBy: 'أنشئت بواسطة',
                filterByEmployee: 'تصفية حسب الموظف المسئول',
                invoiceCreatedBy: 'تم إنشاء الفاتورة بواسطة',
                expenses: 'المصاريف والمنصرفات',
                homeContent: 'إدارة المحتوى',
                users: 'فريق العمل والمستخدمين',
                roles: 'الأدوار والصلاحيات',
                selectCustomer: 'اختر عميل من القائمة',
                addCustomer: 'إضافة عميل جديد',
                customerPlaceholder: 'ابحث بالاسم أو رقم الموبايل...',
                totalPurchases: 'إجمالي المشتروات',
                ordersCount: 'عدد الطلبات الكلي',
                lastPurchase: 'تاريخ آخر عملية',
                totalPaid: 'إجمالي المدفوعات',
                debt: 'المديونية الحالية',
                vip: 'عميل مميز (VIP)',
                regular: 'عميل اعتيادي',
                newCustomer: 'عميل جديد',
                customerName: 'اسم العميل الثنائي/الثلاثي',
                phone: 'رقم الموبايل',
                notes: 'ملاحظات الموظف',
                save: 'حفظ البيانات',
                mustSelectCustomer: 'لازم تختار عميل الأول عشان تكمل',
                history: 'سجل المشتريات السابق',
                // New Admin Portal Keys
                businessIntelligence: 'ذكاء مؤشرات الأعمال',
                inventoryAlerts: 'تنبيهات نواقص المخزون',
                topPerformers: 'الأصناف الأكثر مبيعاً',
                transactionHistory: 'سجل الحركات المالية',
                lifecycleManagement: 'إدارة دورة العمل',
                clientProfile: 'ملف بيانات العميل',
                itemizedSettlement: 'بيان تفصيلي بالحساب',
                procurementAlerts: 'تنبيهات التوريد والشراء',
                attendanceLog: 'سجل الحضور والانصراف',
                cashboxControl: 'إدارة خزينة النقدية',
                securityCenter: 'مركز التحكم الأمني',
                auditTrail: 'سجل العمليات والرقابة',
                systemGovernance: 'حوكمة وإدارة النظام',
            }
        };
        if (!translations[this.language]) {
            // Fallback to ar if current language is not found
            return translations['ar'][key] || translations['en'][key] || key;
        }
        return translations[this.language][key] || key;
    }
});
