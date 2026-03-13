import Link from 'next/link';
import ProductCard from '@/components/ProductCard';
import { Product } from '@/types';

async function getFeaturedProducts() {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products?limit=4`, { cache: 'no-store' });
    const data = await res.json();
    return data.data;
}

export default async function HomePage() {
    const products = await getFeaturedProducts();

    return (
        <main className="min-h-screen">
            <section className="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20 px-8 text-center">
                <h1 className="text-5xl font-extrabold mb-4">Premium Tech Accessories</h1>
                <p className="text-blue-100 text-lg mb-8">The best gear for developers and creators</p>
                <Link href="/products" className="bg-white text-blue-600 px-8 py-3 rounded-full font-semibold hover:bg-blue-50 transition">
                    Shop Now →
                </Link>
            </section>

            <section className="max-w-6xl mx-auto px-8 py-16">
                <h2 className="text-2xl font-bold text-gray-900 mb-8">Featured Products</h2>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {products?.map((product: Product) => (
                        <ProductCard key={product.id} product={product} />
                    ))}
                </div>
                <div className="text-center mt-10">
                    <Link href="/products" className="border border-blue-600 text-blue-600 px-8 py-3 rounded-full hover:bg-blue-50 transition">
                        View All Products →
                    </Link>
                </div>
            </section>
        </main>
    );
}