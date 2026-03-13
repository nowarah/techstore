import { notFound } from 'next/navigation';
import AddToCartButton from '@/components/AddToCartButton';

async function getProduct(slug: string) {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products/${slug}`, { cache: 'no-store' });
    if (!res.ok) return null;
    const data = await res.json();
    return data.data;
}

export default async function ProductDetailPage({ params }: { params: Promise<{ slug: string }> }) {
    const { slug } = await params;
    const product = await getProduct(slug);

    if (!product) return notFound();

    return (
        <main className="max-w-4xl mx-auto px-8 py-12">
            <div className="bg-white rounded-xl border border-gray-200 p-8 flex gap-10">
                <div className="bg-gray-100 rounded-xl w-64 h-64 flex items-center justify-center flex-shrink-0">
                    <span className="text-7xl">📦</span>
                </div>
                <div className="flex-1">
                    <span className="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                        {product.category.name}
                    </span>
                    <h1 className="text-3xl font-bold text-gray-900 mt-3 mb-2">{product.name}</h1>
                    <p className="text-gray-500 mb-6">{product.description}</p>
                    <p className="text-3xl font-bold text-blue-600 mb-2">{product.priceFormatted}</p>
                    <p className="text-sm text-gray-400 mb-6">
                        {product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
                    </p>
                    <AddToCartButton product={product} />
                </div>
            </div>
        </main>
    );
}