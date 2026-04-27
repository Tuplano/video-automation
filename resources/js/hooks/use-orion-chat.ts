import { useEffect, useRef, useState } from 'react';
import { status, store } from '@/routes/orion/chat';

export type ChatMessage = {
    id: string;
    role: 'user' | 'assistant';
    text: string;
    veoNonce?: string | null;
    generateVideoId?: string | null;
    resultNonce?: string | null;
    videoUrl?: string | null;
    generateNonceFound?: boolean;
    generateVideoIdFound?: boolean;
    resultNonceFound?: boolean;
    videoUrlFound?: boolean;
    videoStatus?: 'processing' | 'ready' | 'unavailable' | 'failed';
    videoError?: string | null;
};

type OrionResponse = {
    text: string;
    conversation_id: string | null;
    veo_nonce: string | null;
    generate_video_id: string | null;
    result_nonce: string | null;
    video_url: string | null;
    video_status: 'processing' | 'ready' | 'unavailable';
    generate_nonce_found: boolean;
    generate_video_id_found: boolean;
    result_nonce_found: boolean;
    video_url_found: boolean;
};

type OrionVideoStatusResponse = {
    generate_video_id: string;
    result_nonce: string | null;
    video_url: string | null;
    video_status: 'processing' | 'ready';
    result_nonce_found: boolean;
    video_url_found: boolean;
};

const initialMessages: ChatMessage[] = [
    {
        id: 'assistant-intro',
        role: 'assistant',
        text: 'Ask for a city and I will give you a weather-style answer. Guest requests are open, so each message is handled on its own.',
    },
];

export function useOrionChat() {
    const [message, setMessage] = useState('');
    const [messages, setMessages] = useState<ChatMessage[]>(initialMessages);
    const [conversationId, setConversationId] = useState<string | null>(null);
    const [error, setError] = useState<string | undefined>();
    const [isSending, setIsSending] = useState(false);

    const transcriptRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        transcriptRef.current?.scrollTo({
            top: transcriptRef.current.scrollHeight,
            behavior: 'smooth',
        });
    }, [messages, isSending]);

    async function waitForVideoResult(
        messageId: string,
        generateVideoId: string,
        nonce: string,
    ): Promise<void> {
        try {
            const response = await fetch(
                status.url({
                    query: {
                        generate_video_id: generateVideoId,
                        nonce,
                    },
                }),
                {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                },
            );

            if (!response.ok) {
                throw new Error('The video is still processing or unavailable.');
            }

            const data = (await response.json()) as OrionVideoStatusResponse;

            console.log('[Orion Chat] Video status payload', data);

            setMessages((currentMessages) =>
                currentMessages.map((chatMessage) =>
                    chatMessage.id === messageId
                        ? {
                              ...chatMessage,
                              resultNonce: data.result_nonce,
                              videoUrl: data.video_url,
                              resultNonceFound: data.result_nonce_found,
                              videoUrlFound: data.video_url_found,
                              videoStatus: data.video_status,
                              videoError: null,
                          }
                        : chatMessage,
                ),
            );
        } catch (requestError) {
            const fallbackMessage =
                requestError instanceof Error
                    ? requestError.message
                    : 'The video is still processing or unavailable.';

            setMessages((currentMessages) =>
                currentMessages.map((chatMessage) =>
                    chatMessage.id === messageId
                        ? {
                              ...chatMessage,
                              videoStatus: 'failed',
                              videoError: fallbackMessage,
                          }
                        : chatMessage,
                ),
            );
        }
    }

    async function sendMessage(nextMessage: string): Promise<void> {
        const trimmedMessage = nextMessage.trim();

        if (!trimmedMessage || isSending) {
            return;
        }

        setIsSending(true);
        setError(undefined);
        setMessages((currentMessages) => [
            ...currentMessages,
            {
                id: `user-${crypto.randomUUID()}`,
                role: 'user',
                text: trimmedMessage,
            },
        ]);
        setMessage('');

        try {
            const response = await fetch(store.url(), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify({
                    message: trimmedMessage,
                    conversation_id: conversationId,
                }),
            });

            if (response.status === 429) {
                throw new Error(
                    'Too many requests for now. Wait a minute and try again.',
                );
            }

            if (!response.ok) {
                throw new Error('Orion could not answer that request.');
            }

            const data = (await response.json()) as OrionResponse;

            console.log('[Orion Chat] Received response payload', data);
            console.log('[Orion Chat] Getting nonce', {
                found: data.generate_nonce_found,
                nonce: data.veo_nonce,
            });
            console.log('[Orion Chat] Getting video id', {
                found: data.generate_video_id_found,
                generateVideoId: data.generate_video_id,
            });
            console.log('[Orion Chat] Generating video url', {
                videoStatus: data.video_status,
                resultNonceFound: data.result_nonce_found,
                resultNonce: data.result_nonce,
                videoUrlFound: data.video_url_found,
                videoUrl: data.video_url,
            });

            setConversationId(data.conversation_id);
            const assistantMessageId = `assistant-${crypto.randomUUID()}`;

            setMessages((currentMessages) => [
                ...currentMessages,
                {
                    id: assistantMessageId,
                    role: 'assistant',
                    text: data.text,
                    veoNonce: data.veo_nonce,
                    generateVideoId: data.generate_video_id,
                    resultNonce: data.result_nonce,
                    videoUrl: data.video_url,
                    generateNonceFound: data.generate_nonce_found,
                    generateVideoIdFound: data.generate_video_id_found,
                    resultNonceFound: data.result_nonce_found,
                    videoUrlFound: data.video_url_found,
                    videoStatus: data.video_status,
                    videoError: null,
                },
            ]);

            if (data.video_status === 'processing' && data.generate_video_id && data.veo_nonce) {
                void waitForVideoResult(
                    assistantMessageId,
                    data.generate_video_id,
                    data.veo_nonce,
                );
            }
        } catch (requestError) {
            const fallbackMessage =
                requestError instanceof Error
                    ? requestError.message
                    : 'Something went wrong while sending your message.';

            setError(fallbackMessage);
        } finally {
            setIsSending(false);
        }
    }

    return {
        error,
        isSending,
        message,
        messages,
        sendMessage,
        setMessage,
        transcriptRef,
    };
}
