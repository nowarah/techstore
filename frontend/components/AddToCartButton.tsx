'use client';

import { useCart } from '@/store/cartStore';
import { useState } from 'react';
import { Product } from '@/types';

export default function AddToCartButton({ product }: { product: Product }) {
    const { addItem, loading } = useCart();
    const [quantity, setQuantity] = useState(1);
    const [added, setAdded] = useState(false);

    async function handleAdd() {
        await addItem(product.id, quantity);
        setAdded(true);
        setTimeout(() => setAdded(false), 2000);
    }

    return (
        <div className="flex gap-3 items-center">
            <input
                type="number"
                min={1}
                max={product.stock}
                value={quantity}
                onChange={e => setQuantity(parseInt(e.target.value))}
                className="w-16 border border-gray-300 rounded-lg px-3 py-2 text-center"
            />
            <button
                onClick={handleAdd}
                disabled={loading || product.stock === 0}
                className="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition disabled:opacity-50"
            >
                {added ? '✓ Added!' : 'Add to Cart'}
            </button>
        </div>
    );
}