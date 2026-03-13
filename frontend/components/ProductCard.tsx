'use client';

import Link from 'next/link';
import { useCart } from '@/store/cartStore';

export default function ProductCard({ product }: { product: any }) {
    const { addItem, loading } = useCart();

    return (
        <div className="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition">
            <div className="bg-gray-100 rounded-lg h-40 mb-4 flex items-center justify-center">
                <span className="text-4xl">📦</span>
            </div>
            <span className="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                {product.category.name}
            </span>
            <Link href={`/products/${product.slug}`}>
                <h3 className="font-semibold text-gray-900 mt-2 hover:text-blue-600">{product.name}</h3>
            </Link>
            <p className="text-gray-500 text-sm mt-1 line-clamp-2">{product.description}</p>
            <div className="flex justify-between items-center mt-4">
                <span className="font-bold text-gray-900">{product.priceFormatted}</span>
                <button
                    onClick={() => addItem(product.id)}
                    disabled={loading || product.stock === 0}
                    className="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-blue-700 transition disabled:opacity-50"
                >
                    {product.stock === 0 ? 'Out of Stock' : 'Add to Cart'}
                </button>
            </div>
        </div>
    );
}