import ProductCard from '@/components/ProductCard';
import { Product, Category } from '@/types';

async function getProducts() {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/products`, { cache: 'no-store' });
    const data = await res.json();
    return data.data;
}

async function getCategories() {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/categories`, { cache: 'no-store' });
    const data = await res.json();
    return data.data;
}

export default async function ProductsPage() {
    const products = await getProducts();
    const categories = await getCategories();

    return (
        <main className="max-w-6xl mx-auto px-8 py-12">
            <h1 className="text-3xl font-bold text-gray-900 mb-8">All Products</h1>
            <div className="flex gap-3 mb-8 flex-wrap">
                <span className="bg-blue-600 text-white px-4 py-2 rounded-full text-sm cursor-pointer">All</span>
                {categories?.map((cat: Category) => (
                    <span key={cat.id} className="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-full text-sm cursor-pointer hover:border-blue-600 hover:text-blue-600">
                        {cat.name}
                    </span>
                ))}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {products?.map((product: Product) => (
                    <ProductCard key={product.id} product={product} />
                ))}
            </div>
        </main>
    );
}