'use client';

import { useCart } from '@/store/cartStore';

export default function CartDrawer({ open, onClose }: { open: boolean; onClose: () => void }) {
    const { cart, removeItem } = useCart();

    return (
        <>
            {open && <div className="fixed inset-0 bg-black/40 z-40" onClick={onClose} />}
            <div className={`fixed top-0 right-0 h-full w-96 bg-white shadow-xl z-50 transform transition-transform ${open ? 'translate-x-0' : 'translate-x-full'}`}>
                <div className="p-6 flex flex-col h-full">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="text-xl font-bold text-gray-900">Your Cart</h2>
                        <button onClick={onClose} className="text-gray-400 hover:text-gray-600 text-2xl">×</button>
                    </div>

                    {!cart || cart.items.length === 0 ? (
                        <p className="text-gray-400 text-center mt-10">Your cart is empty</p>
                    ) : (
                        <>
                            <div className="flex-1 overflow-y-auto space-y-4">
                                {cart.items.map(item => (
                                    <div key={item.id} className="flex gap-4 border-b pb-4">
                                        <div className="flex-1">
                                            <p className="font-medium text-gray-900 text-sm">{item.product.name}</p>
                                            <p className="text-gray-500 text-xs">Qty: {item.quantity}</p>
                                            <p className="text-blue-600 font-medium text-sm">{item.subtotalFormatted}</p>
                                        </div>
                                        <button
                                            onClick={() => removeItem(item.id)}
                                            className="text-red-400 hover:text-red-600 text-sm"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                ))}
                            </div>
                            <div className="border-t pt-4 mt-4">
                                <div className="flex justify-between font-bold text-gray-900 mb-4">
                                    <span>Total</span>
                                    <span>{cart.totalFormatted}</span>
                                </div>
                                <a
                                    href="/checkout"
                                    className="block w-full bg-blue-600 text-white text-center py-3 rounded-lg font-medium hover:bg-blue-700 transition"
                                >
                                    Checkout
                                </a>
                            </div>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}