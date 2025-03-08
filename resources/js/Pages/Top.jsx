import React from 'react';
import { Head } from '@inertiajs/react'
import { Component as SideMenu } from '../Components/SideMenu'
import LogoutButton from '@/Components/LogoutButton'
import { StudyHeatmap } from '../Components/StudyHeatmap';

export default function Top({ threads, studyDates }) {
  return (
    <>
      <Head title="Top" />
      <div className="flex min-h-screen bg-gray-700">
        <SideMenu threads={threads} />
        <div className="flex-1 p-8">
          <div className="flex justify-between items-center mb-8">
            <h2 className="text-2xl font-bold text-white">English Job Interview Practice Heatmap</h2>
            <LogoutButton onClick={() => {
                // ログアウト処理をここに記述
            }} />
          </div>

          <StudyHeatmap studyData={studyDates} />
        </div>
      </div>
    </>
  )
}
