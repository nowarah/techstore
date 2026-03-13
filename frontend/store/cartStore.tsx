'use client';

import { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { getCart, addToCart, removeFromCart } from '@/lib/cartClient';
import { Cart } from '@/types';

type CartContextType = {
    cart: Cart | null;
    loading: boolean;
    addItem: (productId: number, quantity?: number) => Promise<void>;
    removeItem: (itemId: number) => Promise<void>;
    refreshCart: () => Promise<void>;
}

const CartContext = createContext<CartContextType | null>(null);

export function CartProvider({ children }: { children: ReactNode }) {
    const [cart, setCart] = useState<Cart | null>(null);
    const [loading, setLoading] = useState(false);

    async function refreshCart() {
        try {
            const data = await getCart();
            setCart(data.data);
        } catch (e) {
            setCart(null);
            console.error(e);
        }
    }

    async function addItem(productId: number, quantity: number = 1) {
        setLoading(true);
        try {
            const data = await addToCart(productId, quantity);
            setCart(data.data);
        } finally {
            setLoading(false);
        }
    }

    async function removeItem(itemId: number) {
        setLoading(true);
        try {
            const data = await removeFromCart(itemId);
            setCart(data.data);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        refreshCart();
    }, []);

    return (
        <CartContext.Provider value={{ cart, loading, addItem, removeItem, refreshCart }}>
            {children}
        </CartContext.Provider>
    );
}

export function useCart() {
    const context = useContext(CartContext);
    if (!context) throw new Error('useCart must be used within CartProvider');
    return context;
}