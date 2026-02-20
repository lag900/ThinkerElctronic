<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';

// Roles and permissions data
const roles = ref([
    { 
        id: 1, 
        name: 'SuperAdmin', 
        description: 'Full system authorization with clearance level 10.',
        permissions: ['inventory', 'finance', 'roles', 'orders', 'customers', 'settings']
    },
    { 
        id: 2, 
        name: 'Manager', 
        description: 'Operational control for orders and inventory management.',
        permissions: ['inventory', 'orders', 'customers']
    },
    { 
        id: 3, 
        name: 'Employee', 
        description: 'Limited access for processing transmissions and stock updates.',
        permissions: ['inventory', 'orders']
    },
]);

const modules = [
    { key: 'inventory', name: 'Inventory Engine' },
    { key: 'finance', name: 'Finance Analytics' },
    { key: 'roles', name: 'Identity Management' },
    { key: 'orders', name: 'Logistics Protocol' },
    { key: 'customers', name: 'Subject dossiers' },
    { key: 'settings', name: 'Core Parameters' },
];

const togglePermission = (roleId, permission) => {
    const role = roles.value.find(r => r.id === roleId);
    if (!role) return;
    
    if (role.permissions.includes(permission)) {
        role.permissions = role.permissions.filter(p => p !== permission);
    } else {
        role.permissions.push(permission);
    }
};
</script>

<template>
    <Head title="System Parameters" />

    <AdminLayout>
        <template #header>Core settings</template>

        <div class="space-y-12">
            <!-- Role Architecture -->
            <div>
                <div class="mb-8">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Identity Architecture</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manage Clearance Levels and Permission Arrays</p>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    <div v-for="role in roles" :key="role.id" class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-700">
                        <div class="mb-10">
                            <h4 class="text-lg font-black text-slate-900 tracking-tight mb-2">{{ role.name }}</h4>
                            <p class="text-xs font-bold text-slate-400 tracking-wide line-clamp-2 h-8">{{ role.description }}</p>
                        </div>

                        <div class="space-y-4">
                            <div v-for="mod in modules" :key="mod.key" class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-2xl transition-all group">
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500 group-hover:text-indigo-600 transition-colors">{{ mod.name }}</span>
                                <div 
                                    @click="togglePermission(role.id, mod.key)"
                                    class="w-10 h-5 rounded-full transition-all cursor-pointer p-1"
                                    :class="role.permissions.includes(mod.key) ? 'bg-indigo-600 shadow-lg shadow-indigo-100' : 'bg-slate-200'"
                                >
                                    <div class="w-3 h-3 bg-white rounded-full shadow-sm transition-all" :class="role.permissions.includes(mod.key) ? 'ml-5' : 'ml-0'"></div>
                                </div>
                            </div>
                        </div>

                        <button class="w-full mt-10 py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200">
                            Synch role
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Other settings? -->
        </div>
    </AdminLayout>
</template>
