<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    name_ar: '',
    sku: '',
    description_en: '',
    description_ar: '',
    price: '',
    cost_price: '',
    stock_quantity: '',
    category_id: '',
    youtube_url: '',
    main_image: null,
    gallery_images: [], // File objects
    specs: [{ label: '', value: '' }],
    codes: []
});

const submit = () => {
    form.post(route('admin.products.store'), {
        forceFormData: true,
    });
};

const addSpec = () => {
    form.specs.push({ label: '', value: '' });
};

const removeSpec = (index) => {
    form.specs.splice(index, 1);
};

const addCode = () => {
    form.codes.push({ title: '', language: 'javascript', code: '' });
};

const removeCode = (index) => {
    form.codes.splice(index, 1);
};

const handleGalleryUpload = (e) => {
    form.gallery_images = Array.from(e.target.files);
};
</script>

<template>
    <Head title="Create Product" />

    <AdminLayout>
        <template #header>Create Technical Asset</template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-12 bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm transition-all hover:shadow-2xl">
                    
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Product Name (English)" />
                            <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>
                        <div dir="rtl">
                            <InputLabel value="اسم المنتج (Arabic)" class="text-right" />
                            <TextInput v-model="form.name_ar" type="text" class="mt-1 block w-full text-right" />
                        </div>

                        <div>
                            <InputLabel value="Selling Price (EGP)" />
                            <TextInput v-model="form.price" type="number" step="0.01" class="mt-1 block w-full" required />
                        </div>
                        <div>
                            <InputLabel value="Cost Price (EGP) - Hidden from customers" />
                            <TextInput v-model="form.cost_price" type="number" step="0.01" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Warehouse Stock" />
                            <TextInput v-model="form.stock_quantity" type="number" class="mt-1 block w-full" />
                        </div>
                        <div>
                            <InputLabel value="Category Architecture" />
                            <select v-model="form.category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="" disabled>Select Segment</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2">
                            <InputLabel value="YouTube Video URL (Optional)" />
                            <TextInput v-model="form.youtube_url" type="url" placeholder="https://youtube.com/watch?v=..." class="mt-1 block w-full" />
                        </div>

                        <div>
                            <InputLabel value="SKU (Unique Identifier)" />
                            <TextInput v-model="form.sku" type="text" placeholder="e.g. THK-MCU-001" class="mt-1 block w-full" />
                            <div v-if="form.errors.sku" class="text-red-500 text-xs mt-1">{{ form.errors.sku }}</div>
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel value="Description (English)" />
                            <textarea v-model="form.description_en" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <div dir="rtl">
                            <InputLabel value="الوصف (Arabic)" class="text-right" />
                            <textarea v-model="form.description_ar" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-right"></textarea>
                        </div>
                    </div>

                    <!-- Images -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-gray-50 rounded-xl">
                        <div>
                            <InputLabel value="Main Image (Required)" />
                            <input type="file" @input="form.main_image = $event.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required accept="image/*" />
                            <div v-if="form.errors.main_image" class="text-red-500 text-xs mt-1">{{ form.errors.main_image }}</div>
                        </div>
                        <div>
                            <InputLabel value="Gallery Images (Optional)" />
                            <input type="file" multiple @input="handleGalleryUpload" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" accept="image/*" />
                        </div>
                    </div>

                    <!-- Dynamic Specs -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Technical Specifications</h3>
                            <SecondaryButton @click="addSpec" type="button">+ Add Spec</SecondaryButton>
                        </div>
                        <div v-for="(spec, index) in form.specs" :key="index" class="flex gap-4 items-start">
                            <TextInput v-model="spec.label" placeholder="Label (e.g. Dimensions)" class="flex-1" />
                            <TextInput v-model="spec.value" placeholder="Value (e.g. 10x10mm)" class="flex-1" />
                            <button @click="removeSpec(index)" type="button" class="text-red-500 hover:text-red-700 font-bold px-2 py-2">X</button>
                        </div>
                    </div>

                    <!-- Dynamic Code Blocks -->
                    <div class="space-y-4 pt-6 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900">Code Blocks (Developer Docs)</h3>
                            <SecondaryButton @click="addCode" type="button">+ Add Code Block</SecondaryButton>
                        </div>
                        <div v-for="(block, index) in form.codes" :key="index" class="space-y-3 p-4 bg-gray-900 rounded-xl border border-gray-700">
                            <div class="flex gap-4">
                                <TextInput v-model="block.title" placeholder="Title (Optional)" class="flex-1 bg-gray-800 text-white border-gray-700" />
                                <select v-model="block.language" class="bg-gray-800 text-white border-gray-700 rounded-md">
                                    <option value="javascript">JavaScript</option>
                                    <option value="python">Python</option>
                                    <option value="cpp">C++</option>
                                    <option value="arduino">Arduino</option>
                                    <option value="html">HTML</option>
                                </select>
                                <button @click="removeCode(index)" type="button" class="text-red-400 hover:text-red-300 font-bold px-2">Remove</button>
                            </div>
                            <textarea v-model="block.code" rows="4" placeholder="// Paste your code here..." class="w-full bg-gray-800 text-green-400 font-mono text-sm rounded-md border-gray-700 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-200">
                        <Link :href="route('admin.products.index')" class="mr-4 px-4 py-2 text-gray-600 hover:text-gray-900">Cancel</Link>
                        <PrimaryButton :disabled="form.processing">Create Product</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
