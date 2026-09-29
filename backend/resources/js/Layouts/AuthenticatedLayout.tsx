import { Link, router, usePage } from '@inertiajs/react';
import { Bell, Globe2, Menu, ShieldCheck } from 'lucide-react';
import { PropsWithChildren, ReactNode, useEffect, useState } from 'react';
import { PageProps } from '@/types';
import Navbars from '@/Components/ui/navbars';

type Notification = { id: number; type: string; read_at: string | null };

export default function AuthenticatedLayout({ header, children }: PropsWithChildren<{ header?: ReactNode }>) {
    const { auth, headerNotifications } = usePage<PageProps & { headerNotifications?: Notification[] }>().props;
    const [mobileOpen, setMobileOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(() => typeof window !== 'undefined' && window.localStorage.getItem('rfid.sidebar.collapsed') === 'true');
    const [notificationsOpen, setNotificationsOpen] = useState(false);
    const path = typeof window === 'undefined' ? '' : window.location.pathname;
    const role = auth.user.role === 'admin' ? 'Admin' : 'Viewer';

    const contentOffset = collapsed ? 'lg:pl-16' : 'lg:pl-64';
    useEffect(() => {
        window.localStorage.setItem('rfid.sidebar.collapsed', String(collapsed));
    }, [collapsed]);

    return <div className="min-h-screen bg-slate-50 text-slate-900">
        <Navbars path={path} role={role} userName={auth.user.name} collapsed={collapsed} mobileOpen={mobileOpen} onToggleCollapsed={() => setCollapsed((value) => !value)} onCloseMobile={() => setMobileOpen(false)} />
        <div className={`${contentOffset} transition-[padding-left] duration-150 ease-out`}>
            <header className="sticky top-0 z-20 flex min-h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-7"><div className="flex min-w-0 items-center gap-3"><button type="button" onClick={() => setMobileOpen(true)} className="rounded-lg border border-slate-200 p-2 transition-colors hover:border-[#173f78] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#173f78] lg:hidden" aria-label="Buka navigasi"><Menu size={20} /></button>{header}</div><div className="relative flex shrink-0 items-center gap-2 sm:gap-3"><button type="button" onClick={() => setNotificationsOpen((value) => !value)} className="relative rounded-lg border border-slate-200 p-2 transition-colors hover:border-[#173f78] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#173f78]" aria-label="Buka notifikasi" aria-expanded={notificationsOpen}><Bell size={18} />{headerNotifications?.some((item) => !item.read_at) && <span className="absolute right-1 top-1 h-2 w-2 rounded-full bg-rose-500" />}</button>{notificationsOpen && <div className="absolute right-0 top-12 z-50 w-80 rounded-xl border border-slate-200 bg-white p-3 text-sm shadow-xl" role="dialog" aria-label="Notifikasi terbaru"><p className="px-2 pb-2 font-bold text-slate-900">Notifikasi terbaru</p>{headerNotifications?.length ? headerNotifications.map((item) => <p key={item.id} className="border-t border-slate-100 px-2 py-3 text-slate-600">{item.type.replaceAll('.', ' · ')}</p>) : <p className="px-2 py-3 text-slate-500">Belum ada notifikasi.</p>}<Link href="/profile#notifikasi" onClick={() => setNotificationsOpen(false)} className="mt-2 block border-t border-slate-100 px-2 pt-3 font-bold text-[#173f78]">Pengaturan notifikasi</Link></div>}<button type="button" onClick={() => router.patch('/locale', { locale: auth.user?.locale === 'en' ? 'id' : 'en' }, { preserveScroll: true })} className="flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 px-3 text-xs font-bold text-slate-600 transition-colors hover:border-[#173f78] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#173f78]" aria-label="ID / EN"><Globe2 size={16} /> {auth.user?.locale === 'en' ? 'EN' : 'ID'} <span className="text-slate-300">/</span> {auth.user?.locale === 'en' ? 'ID' : 'EN'}</button><span className="hidden items-center gap-2 rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 sm:flex"><ShieldCheck size={15} /> Sesi aman</span></div></header>
            <main className="p-4 sm:p-7">{children}</main>
        </div>
    </div>;
}
