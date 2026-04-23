import { Link } from '@inertiajs/react';

export default function NavLink({ active = false, className = '', children, ...props }) {
    return (
        <Link
            {...props}
            className={`
                group flex items-center gap-3 border-l-4 py-3 pl-5 pr-6 text-sm font-medium transition-all
                ${active
                    ? 'border-[var(--primary-color)] bg-[var(--sidebar-surface)] text-[var(--text-on-dark)]'
                    : 'border-transparent text-[var(--text-dark-muted)] hover:bg-slate-900/50 hover:text-slate-100'
                }
                ${className}
            `}
        >
            {children}
        </Link>
    );
}
