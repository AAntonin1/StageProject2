<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { request } from '@/routes/password';
import { Head, useForm } from '@inertiajs/vue3';

// Props du composant
const props = defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const openRegister = () => {
    closeLoginModal();
    showRegisterModal.value = true;
};

// Formulaire Inertia
const form = useForm({
    email: '',
    password: '',
    remember: false
});

// Soumission du formulaire
const submit = () => {
    form.post('/login', {
        onSuccess: () => {
            closeLoginModal(); // Ferme la modale après login réussi
        }
    });
};
</script>

<template>
    <AuthBase
        title="Connexion à votre compte"
        description="Entrez votre email et mot de passe ci-dessous pour vous connecter"
    >
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Adresse email</Label>
                    <Input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Mot de passe</Label>
                        <TextLink v-if="props.canResetPassword" :href="request()" class="text-sm" :tabindex="5">
                            Mot de passe oublié?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="Mot de passe"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Se souvenir</span>
                    </Label>
                </div>

                <!-- Submit -->
                <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Connexion
                </Button>
            </div>

            <!-- Register link -->
            <div class="text-center text-sm text-muted-foreground" v-if="props.canRegister">
                Pas encore de compte?
                <TextLink href="#" @click="openRegister" :tabindex="5">S'inscrire</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
