import { cookies } from 'next/headers';
import NavbarClient from './NavbarClient';

export default async function Navbar() {
    const cookieStore = await cookies();
    const isLoggedIn = !!cookieStore.get('token');

    return <NavbarClient isLoggedIn={isLoggedIn} />;
}