import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import { useEffect, useState } from 'react';

function NotificationPreferences() {
    const [operational, setOperational] = useState(true);
    const [status, setStatus] = useState(true);
    useEffect(() => { setOperational(localStorage.getItem('rfid.notify.operational') !== 'off'); setStatus(localStorage.getItem('rfid.notify.status') !== 'off'); }, []);
    const save = (key: string, value: boolean, setter: (next: boolean) => void) => { setter(value); localStorage.setItem(key, value ? 'on' : 'off'); };
    return <section id="notifikasi" className="bg-white p-4 shadow sm:rounded-lg sm:p-8"><div className="max-w-xl"><h3 className="text-lg font-bold text-slate-900">Preferensi notifikasi</h3><p className="mt-1 text-sm text-slate-600">Atur jenis pemberitahuan yang ingin ditampilkan di popup lonceng.</p><div className="mt-5 space-y-4"><label className="flex items-start gap-3 text-sm"><input type="checkbox" checked={operational} onChange={(event) => save('rfid.notify.operational', event.target.checked, setOperational)} className="mt-0.5 rounded border-slate-300 text-[#173f78]" /><span><b>Perhatian operasional</b><span className="block text-slate-500">Pengajuan baru, target, dan stok rendah.</span></span></label><label className="flex items-start gap-3 text-sm"><input type="checkbox" checked={status} onChange={(event) => save('rfid.notify.status', event.target.checked, setStatus)} className="mt-0.5 rounded border-slate-300 text-[#173f78]" /><span><b>Perubahan status</b><span className="block text-slate-500">Perubahan proses pengajuan yang Anda tangani.</span></span></label></div></div></section>;
}

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Pengaturan
                </h2>
            }
        >
            <Head title="Pengaturan" />

            <div className="py-5 sm:py-7">
                <div className="mx-auto max-w-5xl space-y-4">
                    <NotificationPreferences />

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdateProfileInformationForm
                            mustVerifyEmail={mustVerifyEmail}
                            status={status}
                            className="max-w-xl"
                        />
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <UpdatePasswordForm className="max-w-xl" />
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
                        <DeleteUserForm className="max-w-xl" />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
