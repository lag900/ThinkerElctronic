<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    packages: Array,
});

const showAddForm = ref(false);

const addForm = useForm({
    points: '',
    price: '',
    payment_link: '',
});

const editingId = ref(null);
const editForm = useForm({
    points: '',
    price: '',
    payment_link: '',
});

const startEdit = (pkg) => {
    editingId.value = pkg.id;
    editForm.points = pkg.points;
    editForm.price = pkg.price;
    editForm.payment_link = pkg.payment_link || '';
};

const cancelEdit = () => {
    editingId.value = null;
    editForm.reset();
};

const saveEdit = () => {
    editForm.put(route('lms-packages.update', editingId.value), {
        preserveScroll: true,
        onSuccess: () => cancelEdit(),
    });
};

const deletePackage = (pkg) => {
    if (confirm(`Delete ${pkg.points} points - ${pkg.price}?`)) {
        router.delete(route('lms-packages.destroy', pkg.id), { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="LMS Point Packages" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Batu LMS Point Packages
                </h2>
                <Link
                    :href="route('home')"
                    class="text-sm text-gray-500 hover:text-gray-700"
                >
                    ← View on Welcome page
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                    <!-- Add new package form -->
                    <div class="p-6 border-b border-gray-200">
                        <button
                            v-if="!showAddForm"
                            @click="showAddForm = true"
                            class="w-full py-3 border-2 border-dashed border-gray-300 rounded-lg text-gray-500 hover:border-red-500 hover:text-red-600 font-medium transition-colors"
                        >
                            + Add new point package
                        </button>

                        <form
                            v-else
                            @submit.prevent="addForm.post(route('lms-packages.store'), { onSuccess: () => { addForm.reset(); showAddForm = false; } })"
                            class="space-y-4"
                        >
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <InputLabel for="points" value="Points" />
                                    <TextInput
                                        id="points"
                                        v-model="addForm.points"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        placeholder="e.g. 100"
                                        required
                                    />
                                    <InputError :message="addForm.errors.points" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="price" value="Price" />
                                    <TextInput
                                        id="price"
                                        v-model="addForm.price"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="e.g. 50 جنيه"
                                        required
                                    />
                                    <InputError :message="addForm.errors.price" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="payment_link" value="Payment Link" />
                                    <TextInput
                                        id="payment_link"
                                        v-model="addForm.payment_link"
                                        type="url"
                                        class="mt-1 block w-full"
                                        placeholder="https://..."
                                    />
                                    <InputError :message="addForm.errors.payment_link" class="mt-1" />
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <PrimaryButton type="submit" :disabled="addForm.processing">
                                    Add Package
                                </PrimaryButton>
                                <SecondaryButton type="button" @click="showAddForm = false">
                                    Cancel
                                </SecondaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Packages list -->
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="pkg in packages"
                            :key="pkg.id"
                            class="p-6 flex items-center justify-between gap-4"
                        >
                            <template v-if="editingId === pkg.id">
                                <form
                                    @submit.prevent="saveEdit"
                                    class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4"
                                >
                                    <div>
                                        <InputLabel value="Points" />
                                        <TextInput
                                            v-model="editForm.points"
                                            type="number"
                                            min="1"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError :message="editForm.errors.points" class="mt-1" />
                                    </div>
                                    <div>
                                        <InputLabel value="Price" />
                                        <TextInput
                                            v-model="editForm.price"
                                            type="text"
                                            class="mt-1 block w-full"
                                            required
                                        />
                                        <InputError :message="editForm.errors.price" class="mt-1" />
                                    </div>
                                    <div>
                                        <InputLabel value="Payment Link" />
                                        <TextInput
                                            v-model="editForm.payment_link"
                                            type="url"
                                            class="mt-1 block w-full"
                                        />
                                        <InputError :message="editForm.errors.payment_link" class="mt-1" />
                                    </div>
                                    <div class="md:col-span-3 flex gap-2">
                                        <PrimaryButton type="submit" :disabled="editForm.processing">
                                            Save
                                        </PrimaryButton>
                                        <SecondaryButton type="button" @click="cancelEdit">
                                            Cancel
                                        </SecondaryButton>
                                    </div>
                                </form>
                            </template>
                            <template v-else>
                                <div class="flex-1">
                                    <span class="font-bold text-lg">{{ pkg.points }} pts</span>
                                    <span class="text-gray-600 mx-2">—</span>
                                    <span class="text-gray-800">{{ pkg.price }}</span>
                                    <a
                                        v-if="pkg.payment_link"
                                        :href="pkg.payment_link"
                                        target="_blank"
                                        rel="noopener"
                                        class="block text-sm text-red-600 hover:underline mt-1 truncate max-w-md"
                                    >
                                        {{ pkg.payment_link }}
                                    </a>
                                    <span v-else class="block text-sm text-gray-400 italic">No link set</span>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <SecondaryButton @click="startEdit(pkg)">
                                        Edit
                                    </SecondaryButton>
                                    <DangerButton @click="deletePackage(pkg)">
                                        Delete
                                    </DangerButton>
                                </div>
                            </template>
                        </div>

                        <div
                            v-if="packages.length === 0"
                            class="p-12 text-center text-gray-500"
                        >
                            No packages yet. Click "Add new point package" above to create one.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
