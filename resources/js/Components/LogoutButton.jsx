import { Link } from '@inertiajs/react';

export default function LogoutButton() {
    return (
        <Link
            href={route('logout')}
            method="post"
            as="button"
            className="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600"
        >
            ログアウト
        </Link>
    );
}
