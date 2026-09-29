import { Link } from '@inertiajs/react';
import {
    Boxes,
    ChevronLeft,
    ChevronRight,
    ClipboardList,
    CreditCard,
    LayoutDashboard,
    LogOut,
    NotebookPen,
    ScrollText,
    Settings,
    X,
} from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

type NavItem = {
    label: string;
    href: string;
    icon: LucideIcon;
};

const defaultItems: NavItem[] = [
    { label: 'Monitoring', href: '/dashboard', icon: LayoutDashboard },
    { label: 'Pengajuan', href: '/requests', icon: ClipboardList },
    { label: 'Inventori RFID', href: '/inventory', icon: Boxes },
    { label: 'Catatan Pengadaan', href: '/procurement-notes', icon: NotebookPen },
    { label: 'Aktivitas', href: '/audit', icon: ScrollText },
    { label: 'Pengaturan', href: '/profile', icon: Settings },
];

type NavbarsProps = {
    path: string;
    role: 'Admin' | 'Viewer';
    userName: string;
    collapsed: boolean;
    mobileOpen: boolean;
    onToggleCollapsed: () => void;
    onCloseMobile: () => void;
    items?: NavItem[];
};

function isActiveRoute(path: string, href: string) {
    return path === href
        || (href === '/requests' && path.startsWith('/requests/'))
        || (href === '/profile' && path.startsWith('/profile'));
}

function NavigationLink({
    item,
    active,
    collapsed,
    onCloseMobile,
}: {
    item: NavItem;
    active: boolean;
    collapsed: boolean;
    onCloseMobile: () => void;
}) {
    const Icon = item.icon;

    return (
        <Link
            href={item.href}
            onClick={onCloseMobile}
            aria-label={item.label}
            aria-current={active ? 'page' : undefined}
            title={collapsed ? item.label : undefined}
            className={`group relative flex min-h-10 items-center rounded-lg text-sm font-medium transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200 ${collapsed ? 'justify-center px-2' : 'gap-3 px-3'} ${active ? 'bg-white text-[#173f78]' : 'text-slate-300 hover:bg-white/10 hover:text-white'}`}
        >
            <Icon size={18} strokeWidth={active ? 2.4 : 2} aria-hidden="true" />
            <span className={collapsed ? 'sr-only' : 'truncate'}>{item.label}</span>
            {collapsed && (
                <span
                    role="tooltip"
                    className="pointer-events-none absolute left-full z-50 ml-2 whitespace-nowrap rounded-md bg-slate-950 px-2.5 py-1.5 text-xs font-semibold text-white opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100 group-focus-visible:opacity-100"
                >
                    {item.label}
                </span>
            )}
        </Link>
    );
}

export default function Navbars({
    path,
    role,
    userName,
    collapsed,
    mobileOpen,
    onToggleCollapsed,
    onCloseMobile,
    items = defaultItems,
}: NavbarsProps) {
    return (
        <>
            {mobileOpen && (
                <button
                    type="button"
                    className="fixed inset-0 z-30 bg-slate-950/40 lg:hidden"
                    onClick={onCloseMobile}
                    aria-label="Tutup menu"
                />
            )}

            <aside
                className={`fixed inset-y-0 left-0 z-40 flex ${collapsed ? 'lg:w-16' : 'lg:w-64'} w-64 flex-col overflow-hidden border-r border-white/10 bg-[#10294d] text-white transition-[width,transform] duration-150 ease-out lg:translate-x-0 ${mobileOpen ? 'translate-x-0' : '-translate-x-full'}`}
                aria-label="Sidebar navigasi"
            >
                <div
                    className={`relative flex h-16 shrink-0 border-b border-white/10 ${collapsed ? 'items-center justify-center px-1' : 'items-center justify-between px-4'}`}
                >
                    <Link
                        href="/dashboard"
                        className={`flex min-w-0 items-center rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200 ${collapsed ? 'h-9 w-9 self-center' : 'justify-center'}`}
                        aria-label="RFID Operations"
                        title={collapsed ? 'RFID Operations' : undefined}
                    >
                        <span className={`grid shrink-0 place-items-center rounded-lg bg-white text-[#173f78] ${collapsed ? 'h-9 w-9' : 'h-10 w-10'}`}>
                            <CreditCard size={19} aria-hidden="true" />
                        </span>
                        <span className={collapsed ? 'sr-only' : 'ml-3 truncate text-sm font-bold tracking-tight'}>
                            RFID Operations
                        </span>
                    </Link>

                    {!collapsed && (
                        <button
                            type="button"
                            onClick={onToggleCollapsed}
                            className="hidden rounded-md p-2 text-white/70 transition-colors duration-150 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200 lg:block"
                            aria-label="Ciutkan sidebar"
                            title="Ciutkan sidebar"
                        >
                            <ChevronLeft size={17} aria-hidden="true" />
                        </button>
                    )}

                    <button
                        type="button"
                        onClick={onCloseMobile}
                        className="absolute right-2 top-3 rounded-md p-2 text-white/70 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200 lg:hidden"
                        aria-label="Tutup navigasi"
                    >
                        <X size={19} aria-hidden="true" />
                    </button>
                </div>

                {collapsed && (
                    <div className="hidden h-8 shrink-0 place-items-center border-b border-white/10 lg:grid">
                        <button
                            type="button"
                            onClick={onToggleCollapsed}
                            className="grid h-6 w-8 place-items-center rounded-md text-white/70 transition-colors duration-150 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-200"
                            aria-label="Buka sidebar"
                            title="Buka sidebar"
                        >
                            <ChevronRight size={16} aria-hidden="true" />
                        </button>
                    </div>
                )}

                <nav
                    className="min-h-0 flex-1 overflow-x-hidden overflow-y-auto px-2 py-4"
                    aria-label="Navigasi utama"
                >
                    <div className="space-y-1">
                        {items.map((item) => (
                            <NavigationLink
                                key={item.href}
                                item={item}
                                active={isActiveRoute(path, item.href)}
                                collapsed={collapsed}
                                onCloseMobile={onCloseMobile}
                            />
                        ))}
                    </div>
                </nav>

                <div className={`shrink-0 border-t border-white/10 ${collapsed ? 'flex flex-col items-center gap-2 px-1 py-3' : 'p-3'}`}>
                    <div className={`flex items-center ${collapsed ? 'flex-col gap-2' : 'gap-3'}`}>
                        <span
                            className={`grid shrink-0 place-items-center rounded-full bg-sky-200 font-bold text-[#173f78] ${collapsed ? 'h-9 w-9 text-xs' : 'h-9 w-9 text-sm'}`}
                            aria-hidden="true"
                        >
                            {userName.slice(0, 1).toUpperCase()}
                        </span>

                        <div className={`min-w-0 flex-1 ${collapsed ? 'sr-only' : ''}`}>
                            <p className="truncate text-sm font-semibold">{userName}</p>
                            <p className="truncate text-xs text-slate-300">{role}</p>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            aria-label="Keluar"
                            title="Keluar"
                            className={`rounded-md text-slate-300 transition-colors duration-150 hover:bg-rose-500/20 hover:text-rose-300 focus-visible:bg-rose-500/20 focus-visible:text-rose-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-300 ${collapsed ? 'p-1.5' : 'p-2'}`}
                        >
                            <LogOut size={17} aria-hidden="true" />
                        </Link>
                    </div>
                </div>
            </aside>
        </>
    );
}

export { defaultItems };
