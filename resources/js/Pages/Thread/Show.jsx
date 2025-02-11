import React from 'react';
import { Head } from '@inertiajs/react'
import { Component as SideMenu } from '../../Components/SideMenu'
import LogoutButton from '@/Components/LogoutButton'

export default function Show({ threads, messages }) {
  // メッセージの送信者を変換する関数
  const getSenderDisplay = (senderCode) => {
    return senderCode === 1 ? "You" : "AI";
  };

  return (
    <>
      <Head title="Show" />
      <div className="flex min-h-screen bg-gray-700">
        <SideMenu threads={threads} />
        <div className="flex-1 h-screen p-8 flex flex-col">
          <div className="flex justify-end mb-8 shrink-0">
            <LogoutButton onClick={() => {
              // ログアウト処理をここに記述
            }} />
          </div>

          <div className="flex-1 overflow-y-auto">
            <div className="mb-20">
              {messages.map((message) => {
                const senderDisplay = getSenderDisplay(message.sender);
                return (
                  <div
                    key={message.id}
                    className={`flex ${senderDisplay === "You" ? "justify-end" : "justify-start"} mb-4`}
                  >
                    <div className={`flex ${senderDisplay === "AI" ? "flex-row" : "flex-row-reverse"} items-center gap-2 max-w-[80%]`}>
                      <div className={`flex items-center gap-2 ${senderDisplay === "You" ? "flex-row-reverse" : "flex-row"}`}>
                        <div className={`w-10 h-10 rounded-full flex items-center justify-center shrink-0 ${
                          senderDisplay === "AI" ? "bg-gray-500" : "bg-green-500"
                        }`}>
                          <span className="text-white text-sm">{senderDisplay}</span>
                        </div>
                        <div className="bg-white rounded-lg p-3">
                          {message.message_en}
                        </div>
                      </div>
                      {senderDisplay === "AI" && (
                        <div className="flex gap-2 ml-2">
                          <button className="bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414z" clipRule="evenodd" />
                            </svg>
                          </button>
                          <button className="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600">
                            A あ
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>

          <div className="h-20 shrink-0 flex items-center justify-center">
            <button className="bg-green-600 p-4 rounded-full hover:bg-green-700 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                <path fillRule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clipRule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </>
  )
}
