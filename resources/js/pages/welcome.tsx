import { Head } from '@inertiajs/react';
import OrionChatPanel from '@/components/orion-chat-panel';
import OrionLandingContent from '@/components/orion-landing-content';
import { useOrionChat } from '@/hooks/use-orion-chat';

export default function Welcome() {
    const { error, isSending, message, messages, sendMessage, setMessage, transcriptRef } =
        useOrionChat();

    return (
        <>
            <Head title="Orion" />
            <OrionLandingContent
                chatPanel={
                    <OrionChatPanel
                        className="min-h-[34rem]"
                        error={error}
                        isSending={isSending}
                        message={message}
                        messages={messages}
                        onMessageChange={setMessage}
                        onSubmitMessage={sendMessage}
                        transcriptRef={transcriptRef}
                    />
                }
                onSelectPrompt={setMessage}
            />
        </>
    );
}
