import { NextResponse } from 'next/server';
import { cookies } from 'next/headers';

export async function DELETE(request: Request, { params }: { params: Promise<{ itemId: string }> }) {
    const { itemId } = await params;
    const cookieStore = await cookies();
    const token = cookieStore.get('token')?.value;

    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/cart/remove/${itemId}`, {
        method: 'DELETE',
        headers: {
            ...(token && { 'Authorization': `Bearer ${token}` }),
        },
    });

    const data = await res.json();
    return NextResponse.json(data);
}