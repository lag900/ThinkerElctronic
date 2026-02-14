import { reactive, computed } from 'vue';

export const cart = reactive({
    items: [],
    currency: 'EGP',
    language: 'en', // 'en' or 'ar'
    
    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.items.push({ ...product, quantity: 1 });
        }
    },
    
    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
    },
    
    updateQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, parseInt(quantity) || 1);
        }
    },
    
    clearCart() {
        this.items = [];
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
                categories: 'Product Categories'
            },
            ar: {
                home: 'الرئيسية',
                shop: 'المتجر',
                printing: 'خدمة الطباعة ثلاثية الأبعاد',
                solutions: 'الحلول البرمجية',
                contact: 'اتصل بنا',
                addToCart: 'إضافة للسلة',
                cart: 'سلتك',
                checkout: 'الدفع',
                emptyCart: 'سلتك فاضية زي الفضاء.',
                startShopping: 'ابدأ التسوق',
                subtotal: 'المجموع الفروعى',
                remove: 'حذف',
                shopNow: 'تسوق الآن',
                explore: 'اكتشف خدماتنا',
                slogan: 'تفكير ذكي. أنظمة ذكية.',
                thinking: 'تفكير.',
                smart: 'ذكي',
                innovation: 'الابتكار في جوهرنا',
                hardware: 'الأجهزة',
                excellence: 'التميز.',
                stayAhead: 'خليك سابق',
                theFuture: 'المستقبل.',
                joinNow: 'انضم الآن',
                ourProducts: 'منتجاتنا',
                discover: 'اكتشف أحدث ابتكاراتنا في التكنولوجيا الذكية.',
                filter: 'تصفية حسب السعر',
                categories: 'أقسام المنتجات'
            }
        };
        return translations[this.language][key] || key;
    }
});
