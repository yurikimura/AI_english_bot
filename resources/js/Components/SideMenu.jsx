"use client";

import { Link } from '@inertiajs/react';

export function Component({ threads }) {
  return (
    <div className="w-64 bg-gray-800 p-4">
      <div className="mb-4">
        <h2 className="text-white text-xl font-bold">Threads</h2>
      </div>
      <div className="space-y-2">
        {threads && threads.map((thread) => (
          <Link
            key={thread.id}
            href={route('threads.show', thread.id)}
            className="block p-2 text-white hover:bg-gray-700 rounded"
          >
            {thread.title || `Thread ${thread.id}`}
          </Link>
        ))}
      </div>
    </div>
  );
}
