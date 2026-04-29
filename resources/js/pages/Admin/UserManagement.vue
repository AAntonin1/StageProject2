<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    users: {
        type: Array
    },
});

const form = useForm({
    first_name: '',
    last_name: '',
    username: '',
    email: '',
    password: '',
    role: 'user',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => form.reset('password'),
    });
};

// Fonction adaptée pour les initiales avec la propriété "name"
const getInitials = (user) => {
    if (!user.name) return '??';
    return user.name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const deleteUser = (userId) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
        form.delete(route('users.destroy', userId), {
            onSuccess: () => {
            },
            onError: (errors) => {
                console.error("Erreur lors de la suppression :", errors);
            }
        });
    }
};


</script>

<template>
    <div class="space-y-8">
        <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-3 border-slate-100 pb-4">
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <h2 class="text-sm font-black uppercase text-slate-700">Créer un nouvel utilisateur</h2>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Prénom</label>
                    <input v-model="form.first_name" type="text" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Jean">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Nom</label>
                    <input v-model="form.last_name" type="text" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Dupont">
                </div>
                <div class="md:col-span-2 space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Nom d'utilisateur</label>
                    <input v-model="form.username" type="text" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="jdupont">
                </div>
                <div class="md:col-span-2 space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Adresse Email</label>
                    <input v-model="form.email" type="email" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="email@exemple.com">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Mot de passe</label>
                    <input v-model="form.password" type="password" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="••••••••">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Rôle</label>
                    <select v-model="form.role" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Administrateur</option>
                    </select>
                </div>
                <button type="submit" :disabled="form.processing" class="md:col-span-2 mt-2 bg-slate-900 text-white py-3 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-blue-200">
                    Ajouter au système
                </button>
            </form>
        </section>

        <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-sm font-black uppercase text-slate-700 tracking-wider">Utilisateurs enregistrés</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-500">Utilisateur</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-500">Email</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-500">Rôle</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase text-slate-500">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-for="user in users" :key="user.id" class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600">
                                    {{ getInitials(user) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800">{{ user.name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">@{{ user.user_name }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2 py-1 text-[10px] font-black uppercase rounded-md',
                                user.role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700'
                            ]">
                                {{ user.roles[0].name || 'user' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button
                                @click="deleteUser(user.id)"
                                class="text-slate-400 hover:text-red-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!users || users.length === 0">
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm italic">
                            Aucun utilisateur trouvé dans le royaume.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</template>
