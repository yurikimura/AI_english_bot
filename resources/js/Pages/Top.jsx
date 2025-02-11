import React from 'react';
import { Head } from '@inertiajs/react'
import { Component as SideMenu } from '../Components/SideMenu'
import LogoutButton from '@/Components/LogoutButton'

export default function Top({ threads }) {
  return (
    <>
      <Head title="Top" />
      <div className="flex min-h-screen bg-gray-700">
        <SideMenu threads={threads} />
        <div className="flex-1 p-8">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-bold text-white">英会話学習記録</h2>
            <LogoutButton onClick={() => {
                // ログアウト処理をここに記述
            }} />
          </div>

          <div className="grid grid-cols-11 gap-2">
            {Array(55).fill(null).map((_, index) => (
              <div
                key={index}
                className={`aspect-square rounded-sm ${
                  index === 10 || index === 20 ? 'bg-green-500' : 'bg-gray-500'
                }`}
              />
            ))}
          </div>
        </div>
      </div>
    </>
  )
}
