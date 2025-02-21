import React, { useState, useRef, useEffect } from 'react';
import { Head } from '@inertiajs/react'
import { Component as SideMenu } from '../../Components/SideMenu'
import LogoutButton from '@/Components/LogoutButton'

export default function Show({ threads, initialMessages = [], threadId }) {
  const [messages, setMessages] = useState(initialMessages || []);
  const [isRecording, setIsRecording] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [playingAudioId, setPlayingAudioId] = useState(null);
  const audioRefs = useRef({});
  const mediaRecorderRef = useRef(null);
  const chunksRef = useRef([]);
  const [displayLanguages, setDisplayLanguages] = useState({});
  const [prevMessagesLength, setPrevMessagesLength] = useState(0);

  // 最新のメッセージの音声を自動再生する処理を追加
  useEffect(() => {
    if (messages.length > prevMessagesLength && prevMessagesLength !== 0) {
      const latestMessage = messages[messages.length - 1];
      // AIからの応答メッセージの場合のみ自動再生
      if (latestMessage.audio_file_path && latestMessage.sender === 2) {
        const audio = new Audio(`/storage/${latestMessage.audio_file_path}`);
        audio.play().catch(error => {
          console.error('音声の再生に失敗しました:', error);
        });
      }
      setPrevMessagesLength(messages.length);
    } else if (prevMessagesLength === 0) {
      // 初期表示時は再生せずにメッセージ数だけ更新
      setPrevMessagesLength(messages.length);
    }
  }, [messages, prevMessagesLength]);

  // SSEの接続を設定
  useEffect(() => {
    const eventSource = new EventSource(`/thread/${threadId}/events`);

    eventSource.onmessage = (event) => {
      const data = JSON.parse(event.data);
      if (data.type === 'message_update') {
        setMessages(prevMessages =>
          prevMessages.map(message =>
            message.id === data.message.id
              ? { ...message, ...data.message }
              : message
          )
        );
      }
    };

    // コンポーネントのアンマウント時にSSE接続を閉じる
    return () => {
      eventSource.close();
    };
  }, [threadId]);

  const startRecording = async () => {
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      mediaRecorderRef.current = new MediaRecorder(stream);
      chunksRef.current = [];

      mediaRecorderRef.current.ondataavailable = (e) => {
        if (e.data.size > 0) {
          chunksRef.current.push(e.data);
        }
      };

      mediaRecorderRef.current.onstop = async () => {
        setIsLoading(true);
        const audioBlob = new Blob(chunksRef.current, { type: 'audio/mp3' });
        const formData = new FormData();
        formData.append('audio', audioBlob, 'audio.mp3');

        try {
          const response = await axios.post(`/thread/${threadId}/message`, formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
            },
          });

          if (response.data.success) {
            setMessages(prevMessages => [
              ...prevMessages,
              response.data.message,
              response.data.message.assistant_message
            ]);
          } else {
            console.error('APIレスポンスエラー:', response.data);
            alert(response.data.message || 'メッセージの送信に失敗しました。');
          }
        } catch (error) {
          console.error('エラー詳細:', error);
          console.error('エラーレスポンス:', error.response?.data);
          alert(error.response?.data?.message || 'サーバーエラーが発生しました。');
        } finally {
          setIsLoading(false);
        }

        chunksRef.current = [];
        if (mediaRecorderRef.current.stream) {
          mediaRecorderRef.current.stream.getTracks().forEach(track => track.stop());
        }
      };

      mediaRecorderRef.current.start();
      setIsRecording(true);
    } catch (error) {
      console.error('Error accessing microphone:', error);
      alert('マイクへのアクセスができませんでした。ブラウザの設定を確認してください。');
    }
  };

  const stopRecording = () => {
    if (mediaRecorderRef.current && mediaRecorderRef.current.state === 'recording') {
      mediaRecorderRef.current.stop();
      setIsRecording(false);
    }
  };

  const handleRecordingClick = () => {
    if (isRecording) {
      stopRecording();
    } else {
      startRecording();
    }
  };

  // 音声再生を制御する関数を追加
  const handleAudioPlay = (messageId, audioPath) => {
    if (!audioRefs.current[messageId]) {
      audioRefs.current[messageId] = new Audio(`/storage/${audioPath}`);
    }

    const audio = audioRefs.current[messageId];

    if (playingAudioId === messageId) {
      // 同じ音声が再生中の場合は停止
      audio.pause();
      audio.currentTime = 0;
      setPlayingAudioId(null);
    } else {
      // 他の音声が再生中の場合は停止
      if (playingAudioId && audioRefs.current[playingAudioId]) {
        audioRefs.current[playingAudioId].pause();
        audioRefs.current[playingAudioId].currentTime = 0;
      }

      // 新しい音声を再生
      audio.play().catch(error => {
        console.error('音声の再生に失敗しました:', error);
      });
      setPlayingAudioId(messageId);

      // 再生終了時の処理
      audio.onended = () => {
        setPlayingAudioId(null);
      };
    }
  };

  // メッセージの送信者を変換する関数
  const getSenderDisplay = (senderCode) => {
    return senderCode === 1 ? "You" : "AI";
  };

  // 翻訳処理を行う関数を追加
  const handleTranslate = async (messageId) => {
    // 現在の表示言語を確認
    const currentLang = displayLanguages[messageId] || 'en';

    if (currentLang === 'en') {
      try {
        const response = await axios.post(`/thread/${threadId}/message/${messageId}/translate`);

        if (response.data.success) {
          setMessages(prevMessages =>
            prevMessages.map(message =>
              message.id === messageId
                ? { ...message, message_ja: response.data.message }
                : message
            )
          );
        }
      } catch (error) {
        console.error('翻訳APIエラー:', error);
      }
    }

    // 表示言語を切り替え
    setDisplayLanguages(prev => ({
      ...prev,
      [messageId]: currentLang === 'en' ? 'ja' : 'en'
    }));
  };

  return (
    <>
      <Head title="Show" />
      {isLoading && (
        <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
          <div className="animate-spin rounded-full h-10 w-10 border-4 border-gray-300 border-t-blue-600"></div>
        </div>
      )}

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
              {messages?.map((message) => {
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
                          {displayLanguages[message.id] === 'ja' && message.message_ja ? message.message_ja : message.message_en}
                        </div>
                      </div>
                      {senderDisplay === "AI" && (
                        <div className="flex gap-2 ml-2">
                          {message.audio_file_path && (
                            <button
                              className={`bg-gray-500 text-white p-2 rounded-full hover:bg-gray-600 ${
                                playingAudioId === message.id ? 'bg-blue-500' : ''
                              }`}
                              onClick={() => handleAudioPlay(message.id, message.audio_file_path)}
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414z" clipRule="evenodd" />
                              </svg>
                            </button>
                          )}
                          <button
                            className={`bg-gray-500 text-white w-9 h-9 rounded-full hover:bg-gray-600 flex items-center justify-center ${
                              displayLanguages[message.id] === 'ja' ? 'bg-blue-500' : ''
                            }`}
                            onClick={() => handleTranslate(message.id)}
                          >
                            Aあ
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
            <button
              className={`${
                isRecording ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'
              } p-4 rounded-full transition-colors ${isLoading ? 'opacity-50 cursor-not-allowed' : ''}`}
              onClick={handleRecordingClick}
              disabled={isLoading}
            >
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
