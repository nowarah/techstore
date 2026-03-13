'use client';

import Link from 'next/link';
import { useCart } from '@/store/cartStore';
import { useState } from 'react';
import CartDrawer from './CartDrawer';

export default function NavbarClient({ isLoggedIn }: { isLoggedIn: boolean }) {
    const { cart } = useCart();
    const [cartOpen, setCartOpen] = useState(false);
    const itemCount = cart?.items?.length ?? 0;

    return (
        <>
            <nav className="bg-white border-b border-gray-100 px-8 py-4 flex justify-between items-center">
                <Link href="/" className="text-xl font-bold text-gray-900">
                    TechStore<span className="text-blue-600">.</span>
                </Link>
                <div className="flex items-center gap-6">
                    {isLoggedIn ? (
                        <form action="/api/auth/logout" method="POST">
                            <button className="text-gray-600 hover:text-blue-600 text-sm">
                                Logout
                            </button>
                        </form>
                    ) : (
                        <>
                            <Link href="/auth/login" className="text-gray-600 hover:text-blue-600 text-sm">
                                Sign In
                            </Link>
                            <Link href="/auth/register" className="bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm hover:bg-gray-200 transition">
                                Sign Up
                            </Link>
                        </>
                    )}
                    <button
                        onClick={() => setCartOpen(true)}
                        className="relative bg-blue-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-blue-700 transition"
                    >
                        Cart
                        {itemCount > 0 && (
                            <span className="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                {itemCount}
                            </span>
                        )}
                    </button>
                </div>
            </nav>
            <CartDrawer open={cartOpen} onClose={() => setCartOpen(false)} />
        </>
    );
}