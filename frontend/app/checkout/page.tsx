'use client';

import { useCart } from '@/store/cartStore';
import { useRouter } from 'next/navigation';
import { useState } from 'react';
import Link from 'next/link';

export default function CheckoutPage() {
    const { cart, refreshCart } = useCart();
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    async function handleCheckout() {
        setLoading(true);
        setError('');

        const res = await fetch('/api/checkout', { method: 'POST' });
        const data = await res.json();

        if (!res.ok) {
            setError(data.message ?? 'Checkout failed');
            setLoading(false);
            return;
        }

        await refreshCart();
        router.push(`/orders/${data.data.id}`);
    }

    if (!cart || cart.items.length === 0) {
        return (
            <main className="max-w-2xl mx-auto px-8 py-12 text-center">
                <h1 className="text-2xl font-bold text-gray-900 mb-4">Your cart is empty</h1>
                <Link href="/products" className="text-blue-600 hover:underline">Continue shopping</Link>
            </main>
        );
    }

    return (
        <main className="max-w-2xl mx-auto px-8 py-12">
            <h1 className="text-2xl font-bold text-gray-900 mb-8">Checkout</h1>

            {error && <div className="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4">{error}</div>}

            <div className="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <h2 className="font-semibold text-gray-900 mb-4">Order Summary</h2>
                {cart.items.map(item => (
                    <div key={item.id} className="flex justify-between py-2 border-b text-sm">
                        <span className="text-gray-700">{item.product.name} × {item.quantity}</span>
                        <span className="font-medium">{item.subtotalFormatted}</span>
                    </div>
                ))}
                <div className="flex justify-between font-bold text-gray-900 mt-4 text-lg">
                    <span>Total</span>
                    <span>{cart.totalFormatted}</span>
                </div>
            </div>

            <button
                onClick={handleCheckout}
                disabled={loading}
                className="w-full bg-blue-600 text-white py-4 rounded-xl font-semibold text-lg hover:bg-blue-700 transition disabled:opacity-50"
            >
                {loading ? 'Processing...' : 'Place Order'}
            </button>
        </main>
    );
}