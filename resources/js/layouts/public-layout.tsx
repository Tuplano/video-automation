import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { dashboard } from '@/routes';

export default function PublicLayout({
    children,
}: {
    children: ReactNode;
}) {
    const { auth } = usePage().props as {
        auth: { user?: unknown | null };
    };

    return (
        <div className="min-h-screen bg-[#050505] text-[#f4efe7]">
            <div className="sticky top-0 z-40 border-b border-white/10 bg-[#050505]/95 backdrop-blur-sm">
                <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-10">
                    <Link href="/" className="flex items-center gap-3">
                        <div className="flex h-9 w-9 items-center justify-center rounded-md bg-[#f97316] text-sm font-semibold text-black">
                            O
                        </div>
                        <div>
                            <p className="text-sm font-medium tracking-[0.18em] text-white/90 uppercase">
                                Orion
                            </p>
                            <p className="text-xs text-white/50">
                                Free AI video generator
                            </p>
                        </div>
                    </Link>

                    <nav className="flex items-center gap-2 text-sm">
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="rounded-md border border-white/10 px-3 py-2 text-white/85 transition hover:bg-white/5"
                            >
                                Dashboard
                            </Link>
                        ) : null}
                    </nav>
                </div>
            </div>

            <main>{children}</main>

            <footer className="border-t border-white/10 bg-[#090909]">
                <div className="mx-auto grid max-w-7xl gap-8 px-6 py-10 lg:grid-cols-[minmax(0,1.2fr)_repeat(3,minmax(0,1fr))] lg:px-10">
                    <div className="max-w-sm">
                        <p className="text-sm font-medium">Orion</p>
                        <p className="mt-3 text-sm leading-6 text-white/55">
                            A free digital product for generating AI videos
                            from prompts, fast concepts, and lightweight
                            creative direction without the usual friction.
                        </p>
                    </div>

                    <div>
                        <p className="text-sm font-medium text-white/85">
                            Explore
                        </p>
                        <div className="mt-3 grid gap-2 text-sm text-white/55">
                            <a href="#services" className="hover:text-white">
                                Services
                            </a>
                        </div>
                    </div>

                    <div>
                        <p className="text-sm font-medium text-white/85">
                            Product
                        </p>
                        <div className="mt-3 grid gap-2 text-sm text-white/55">
                            <span>Guest access</span>
                            <span>Rate limited</span>
                            <span>Standard React requests</span>
                        </div>
                    </div>

                    <div>
                        <p className="text-sm font-medium text-white/85">
                            Status
                        </p>
                        <div className="mt-3 grid gap-2 text-sm text-white/55">
                            {auth.user ? (
                                <Link href={dashboard()} className="hover:text-white">
                                    Dashboard
                                </Link>
                            ) : (
                                <span>Guest mode enabled</span>
                            )}
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    );
}
