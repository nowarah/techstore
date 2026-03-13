export async function getCart() {
    const res = await fetch('/api/cart');
    return res.json();
}

export async function addToCart(productId: number, quantity: number = 1) {
    const res = await fetch('/api/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity }),
    });
    return res.json();
}

export async function removeFromCart(itemId: number) {
    const res = await fetch(`/api/cart/remove/${itemId}`, { method: 'DELETE' });
    return res.json();
}