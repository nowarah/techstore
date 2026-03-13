export type Product = {
    id: number;
    name: string;
    slug: string;
    description: string;
    price: number;
    priceFormatted: string;
    stock: number;
    category: Category;
};

export type Category = {
    id: number;
    name: string;
};

export type CartItem = {
    id: number;
    product: Product;
    quantity: number;
    subtotal: number;
    subtotalFormatted: string;
};

export type Cart = {
    id: number;
    items: CartItem[];
    total: number;
    totalFormatted: string;
};
