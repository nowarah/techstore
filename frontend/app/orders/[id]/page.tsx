export default async function OrderConfirmationPage({ params }: { params: Promise<{ id: string }> }) {
    const { id } = await params;

    return (
        <main className="max-w-2xl mx-auto px-8 py-12 text-center">
            <div className="bg-white rounded-xl border border-gray-200 p-12">
                <div className="text-6xl mb-6">🎉</div>
                <h1 className="text-3xl font-bold text-gray-900 mb-3">Order Confirmed!</h1>
                <p className="text-gray-500 mb-2">Order #{id}</p>
                <p className="text-gray-500 mb-8">Thank you for your purchase. Your order is being processed.</p>
                <a href="/products" className="bg-blue-600 text-white px-8 py-3 rounded-full font-medium hover:bg-blue-700 transition">
                    Continue Shopping
                </a>
            </div>
        </main>
    );
}