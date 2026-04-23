<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    user_name: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('user.login'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error("Erreur lors de l'envoi :", errors);
        },
    });
};
</script>

<template>
    <Head title="Connexion" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center p-4 md:p-8 font-sans text-slate-900">
        <div class="max-w-md w-full mx-auto">

            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-900 text-white rounded-3xl shadow-xl mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-black uppercase tracking-tight text-slate-900">Content de vous revoir</h1>
                <p class="text-slate-500 font-medium mt-2">Accédez à votre gestionnaire de missions</p>
            </div>

            <form @submit.prevent="submit" class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/60 border border-slate-200 p-8 space-y-6">

                <div>
                    <label for="user_name" class="text-[10px] font-black uppercase text-slate-600 ml-1 mb-1 block">Nom d'utilisateur</label>
                    <input
                        id="user_name"
                        v-model="form.user_name"
                        required
                        class="w-full p-4 bg-slate-100 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900 transition-all"
                        placeholder="jdupont"
                    />
                    <div v-if="form.errors.user_name" class="text-red-500 text-xs font-bold mt-2 ml-1">{{ form.errors.user_name }}</div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1 ml-1">
                        <label for="password" class="text-[10px] font-black uppercase text-slate-600">Mot de passe</label>
                        <a href="#" class="text-[10px] font-black uppercase text-blue-600 hover:text-blue-800">Oublié ?</a>
                    </div>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        class="w-full p-4 bg-slate-100 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-slate-900 transition-all"
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="text-red-500 text-xs font-bold mt-2 ml-1">{{ form.errors.password }}</div>
                </div>

                <div class="flex items-center ml-1">
                    <input
                        id="remember"
                        type="checkbox"
                        v-model="form.remember"
                        class="w-5 h-5 border-none bg-slate-100 rounded-lg text-slate-900 focus:ring-0"
                    />
                    <label for="remember" class="ml-3 text-xs font-bold text-slate-600">Se souvenir de moi</label>
                </div>

                <button
                    :disabled="form.processing"
                    class="w-full bg-slate-900 text-white py-5 rounded-[1.5rem] font-black uppercase tracking-widest shadow-lg shadow-slate-300 hover:bg-black transition-all active:scale-[0.98] disabled:opacity-50 mt-4"
                >
                    <span v-if="!form.processing" class="text-base">Se connecter</span>
                    <span v-else class="text-base">Connexion...</span>
                </button>
            </form>

            <div class="text-center mt-8">
                <p class="text-sm font-bold text-slate-500">
                    Pas encore de compte ?
                    <a href="#" class="text-blue-600 hover:underline">Demander un accès</a>
                </p>
            </div>
        </div>
    </div>
</template>
