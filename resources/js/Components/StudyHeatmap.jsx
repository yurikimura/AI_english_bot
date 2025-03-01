import React from 'react';

export function StudyHeatmap({ studyData = [] }) {
    // 過去3ヶ月分（12週）の日付を生成
    const generateDates = () => {
      const dates = [];
      const today = new Date();

      for (let week = 0; week < 12; week++) {
        for (let day = 0; day < 7; day++) {
          const date = new Date(today);
          date.setDate(today.getDate() - ((11 - week) * 7 + (6 - day)));
          dates.push(date);
        }
      }
      return dates;
    };

    const dates = generateDates();

    return (
      <div className="grid grid-cols-12 gap-2">
        {dates.map((date, index) => (
          <div
            key={index}
            className={`aspect-square rounded-sm ${
              studyData.includes(date.toISOString().split('T')[0]) ? 'bg-green-500' : 'bg-gray-500'
            }`}
            title={date.toLocaleDateString()} // マウスホバー時に日付を表示
          />
        ))}
    </div>
  );
}
