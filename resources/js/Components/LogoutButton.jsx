import { useForm } from '@inertiajs/react';

export default function LogoutButton() {
    const { post } = useForm();

    const handleLogout = () => {
        post(route('logout'));
    };

    return (

        <button
        onClick={handleLogout}
        className="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600"
        >
            ログアウト
        </button>
    );
}

