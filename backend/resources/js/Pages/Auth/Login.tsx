import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { FormEventHandler } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        post(route('login'), {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Masuk" />

            <div className="mb-7">
                <p className="text-sm font-medium text-blue-700">Akses internal</p>
                <h1 className="mt-2 text-2xl font-semibold tracking-tight text-slate-950">
                    Masuk ke dashboard
                </h1>
                <p className="mt-2 text-sm leading-6 text-slate-600">
                    Gunakan akun operasional Anda untuk mengelola pengajuan dan persediaan RFID.
                </p>
            </div>

            {status && (
                <div
                    role="status"
                    className="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-800"
                >
                    {status}
                </div>
            )}

            <form className="space-y-5" onSubmit={submit}>
                <div className="space-y-2">
                    <InputLabel htmlFor="email" value="Alamat email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="block w-full rounded-xl border-slate-300 px-3 py-2.5 text-slate-950 shadow-none placeholder:text-slate-400 focus:border-blue-700 focus:ring-blue-700"
                        autoComplete="username"
                        aria-invalid={Boolean(errors.email)}
                        isFocused={true}
                        onChange={(e) => setData('email', e.target.value)}
                    />

                    <InputError message={errors.email} className="text-sm" />
                </div>

                <div className="space-y-2">
                    <InputLabel htmlFor="password" value="Kata sandi" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="block w-full rounded-xl border-slate-300 px-3 py-2.5 text-slate-950 shadow-none placeholder:text-slate-400 focus:border-blue-700 focus:ring-blue-700"
                        autoComplete="current-password"
                        aria-invalid={Boolean(errors.password)}
                        onChange={(e) => setData('password', e.target.value)}
                    />

                    <InputError message={errors.password} className="text-sm" />
                </div>

                <div className="space-y-4 pt-1">
                    <PrimaryButton
                        className="w-full justify-center rounded-xl bg-blue-700 px-4 py-3 text-sm font-semibold normal-case tracking-normal shadow-sm shadow-blue-950/15 hover:bg-blue-800 focus:bg-blue-800 focus:ring-blue-700 active:bg-blue-900 disabled:cursor-not-allowed"
                        disabled={processing}
                    >
                        {processing ? 'Memproses...' : 'Masuk'}
                    </PrimaryButton>

                    {canResetPassword && (
                        <Link
                            href={route('password.request')}
                            className="block w-fit rounded-md text-sm font-medium text-blue-700 underline decoration-blue-300 underline-offset-4 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-2"
                        >
                            Lupa kata sandi?
                        </Link>
                    )}
                </div>
            </form>
        </GuestLayout>
    );
}
