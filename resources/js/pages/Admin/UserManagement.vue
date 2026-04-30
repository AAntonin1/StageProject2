<script setup>
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { computed } from 'vue';

// Import du Header uniquement
import Header from "@/components/ExpenseReports/Header.vue";

const page = usePage();

const props = defineProps({
    users: Array,
    user: Object,
});

// Détecter si on modifie ou on crée
const isEditing = computed(() => !!props.user);

const form = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    email: props.user?.email || '',
    password: '',
    role: props.user?.roles?.[0]?.name || 'user',
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'), {
            onSuccess: () => form.reset(),
        });
    }
};

const deleteUser = (id) => {
    if (confirm("Supprimer cet utilisateur ?")) {
        form.delete(route('users.destroy', id));
    }
};

const getInitials = (u) => {
    const name = u.name || `${u.first_name} ${u.last_name}`;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-4 md:p-8 font-sans text-slate-900">
        <div class="max-w-2xl mx-auto">

            <!-- Affichage du Header (Distance à 0 car on est en Admin) -->
            <Header :totalDistance="0" />

            <!-- Messages d'alerte -->
            <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl">
                {{ page.props.flash.error }}
            </div>

            <!-- Navigation rapide -->
            <div class="flex gap-2 mb-6">
                <Link :href="route('expenseReport.form')" class="px-4 py-2 rounded-xl font-bold shadow bg-white text-slate-600 text-xs">
                    ← Retour Missions
                </Link>
                <div class="px-4 py-2 rounded-xl font-bold shadow bg-slate-900 text-white text-xs">
                    {{ isEditing ? 'Modifier Utilisateur' : 'Gestion Utilisateurs' }}
                </div>
            </div>

            <!-- Formulaire -->
            <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 mb-8">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-xs font-black uppercase text-slate-700 tracking-wider">
                        {{ isEditing ? `Modifier ${props.user.name}` : 'Nouvel Utilisateur' }}
                    </h2>
                    <Link v-if="isEditing" :href="route('users.index')" class="text-[10px] font-bold text-blue-600 uppercase">
                        Annuler et créer nouveau
                    </Link>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Prénom</label>
                        <input v-model="form.first_name" type="text" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Jean">
                        <span v-if="form.errors.first_name" class="text-red-500 text-xs">{{ form.errors.first_name }}</span>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Nom</label>
                        <input v-model="form.last_name" type="text" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="Dupont">
                        <span v-if="form.errors.last_name" class="text-red-500 text-xs">{{ form.errors.last_name }}</span>
                    </div>
                    <div class="md:col-span-2 space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Email</label>
                        <input v-model="form.email" type="email" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" placeholder="email@exemple.com">
                        <span v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</span>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Mot de passe</label>
                        <input v-model="form.password" type="password" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500" :placeholder="isEditing ? 'Laisser vide pour garder' : '••••••••'">
                        <span v-if="form.errors.password" class="text-red-500 text-xs">{{ form.errors.password }}</span>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-600 ml-1">Rôle</label>
                        <select v-model="form.role" class="w-full p-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                        <span v-if="form.errors.role" class="text-red-500 text-xs">{{ form.errors.role }}</span>
                    </div>
                    <button type="submit" :disabled="form.processing" class="md:col-span-2 mt-2 bg-slate-900 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-black transition-all disabled:opacity-50">
                        {{ isEditing ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </form>
            </section>

            <!-- Tableau -->
            <section class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-500">Nom</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-500">Rôle</th>
                        <th class="px-6 py-4 text-right text-[10px] font-black uppercase text-slate-500">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <tr v-for="u in users" :key="u.id" class="hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-[10px] font-bold text-blue-600">
                                    {{ getInitials(u) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-800">{{ u.name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ u.email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                                <span :class="['px-2 py-0.5 text-[10px] font-black uppercase rounded', u.roles?.[0]?.name === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700']">
                                    {{ u.roles?.[0]?.name || 'user' }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <Link :href="route('users.edit', u.id)" class="text-slate-400 hover:text-blue-600 inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </Link>
                            <button @click="deleteUser(u.id)" class="text-slate-400 hover:text-red-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </section>

        </div>
    </div>
</template>
