import { useEffect, useRef, useState } from 'react';
import { store } from '@/routes/orion/chat';

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
};

type OrionResponse = {
    text: string;
    conversation_id: string | null;
    veo_nonce: string | null;
    generate_video_id: string | null;
    result_nonce: string | null;
    video_url: string | null;
    generate_nonce_found: boolean;
    generate_video_id_found: boolean;
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
                resultNonceFound: data.result_nonce_found,
                resultNonce: data.result_nonce,
                videoUrlFound: data.video_url_found,
                videoUrl: data.video_url,
            });

            setConversationId(data.conversation_id);
            setMessages((currentMessages) => [
                ...currentMessages,
                {
                    id: `assistant-${crypto.randomUUID()}`,
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
                },
            ]);
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
