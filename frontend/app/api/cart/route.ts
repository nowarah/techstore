import { NextResponse } from 'next/server';
import { cookies } from 'next/headers';

export async function GET() {
    const cookieStore = await cookies();
    const token = cookieStore.get('token')?.value;

    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/cart`, {
        headers: {
            ...(token && { 'Authorization': `Bearer ${token}` }),
        },
    });

    const data = await res.json();
    return NextResponse.json(data);
}