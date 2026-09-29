import { render, screen } from '@testing-library/react';
import { describe, expect, it, vi } from 'vitest';

import Login from '../Pages/Auth/Login';

vi.mock('@inertiajs/react', () => ({
    Head: () => null,
    Link: ({ children, ...props }: React.AnchorHTMLAttributes<HTMLAnchorElement>) => (
        <a {...props}>{children}</a>
    ),
    useForm: () => ({
        data: { username: '', password: '' },
        setData: vi.fn(),
        post: vi.fn(),
        processing: false,
        errors: {},
        reset: vi.fn(),
    }),
}));

vi.mock('@/Layouts/GuestLayout', () => ({
    default: ({ children }: { children: React.ReactNode }) => <main>{children}</main>,
}));

vi.stubGlobal('route', (name: string) => `/${name}`);

describe('Login', () => {
    it('presents an Indonesian, password-manager-friendly sign-in form', () => {
        render(<Login canResetPassword />);

        expect(screen.getByRole('heading', { name: 'Masuk ke dashboard' })).toBeVisible();
        expect(screen.getByLabelText('Nama pengguna')).toHaveAttribute('autocomplete', 'username');
        expect(screen.getByLabelText('Kata sandi')).toHaveAttribute('autocomplete', 'current-password');
        expect(screen.getByRole('button', { name: 'Masuk' })).toBeVisible();
        expect(screen.getByRole('link', { name: 'Lupa kata sandi?' })).toBeVisible();
    });
});
