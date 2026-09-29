import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-[100dvh] flex-col items-center bg-slate-50 px-4 py-8 sm:justify-center sm:py-12">
            <main className="w-full max-w-md">
                <Link
                    href="/"
                    className="mb-7 inline-flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-700 focus:ring-offset-4"
                >
                    <span className="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-700 shadow-sm shadow-blue-950/15">
                        <ApplicationLogo className="h-7 w-7 fill-current text-white" />
                    </span>
                    <span className="text-left">
                        <span className="block text-sm font-semibold tracking-tight text-slate-950">
                            RFID Card Management
                        </span>
                        <span className="block text-xs text-slate-500">
                            Ruang kerja internal
                        </span>
                    </span>
                </Link>

                <div className="w-full overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_16px_40px_rgba(15,23,42,0.08)] sm:p-8">
                {children}
                </div>
            </main>
        </div>
    );
}
