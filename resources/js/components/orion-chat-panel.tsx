import {
    type FormEvent,
    type KeyboardEvent,
    type RefObject,
} from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { ChatMessage } from '@/hooks/use-orion-chat';
import { cn } from '@/lib/utils';

type OrionChatPanelProps = {
    className?: string;
    error?: string;
    isSending: boolean;
    message: string;
    messages: ChatMessage[];
    onMessageChange: (message: string) => void;
    onSubmitMessage: (message: string) => Promise<void>;
    transcriptRef: RefObject<HTMLDivElement | null>;
};

export default function OrionChatPanel({
    className,
    error,
    isSending,
    message,
    messages,
    onMessageChange,
    onSubmitMessage,
    transcriptRef,
}: OrionChatPanelProps) {
    function handleSubmit(event: FormEvent<HTMLFormElement>): void {
        event.preventDefault();

        void onSubmitMessage(message);
    }

    function handleKeyDown(event: KeyboardEvent<HTMLTextAreaElement>): void {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            void onSubmitMessage(message);
        }
    }

    return (
        <section
            className={cn(
                'flex min-h-[32rem] flex-col rounded-xl border border-white/10 bg-[#121212] text-[#f4efe7]',
                className,
            )}
        >
            <div className="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <div>
                    <p className="text-sm font-medium">Chat</p>
                    <p className="text-sm text-white/55">
                        Replies appear here as soon as Orion answers.
                    </p>
                </div>
                <div className="text-sm text-white/45">
                    {isSending ? 'Sending' : 'Ready'}
                </div>
            </div>

            <div
                ref={transcriptRef}
                className="flex-1 space-y-4 overflow-y-auto px-5 py-5"
            >
                {messages.map((chatMessage) => (
                    <div
                        key={chatMessage.id}
                        className={
                            chatMessage.role === 'user'
                                ? 'ml-auto max-w-[85%] rounded-lg bg-[#f97316] px-4 py-3 text-sm leading-6 text-black'
                                : 'max-w-[85%] rounded-lg border border-white/10 bg-[#1a1a1a] px-4 py-3 text-sm leading-6 text-[#f5f2ea]'
                        }
                    >
                        {chatMessage.text}
                    </div>
                ))}

                {isSending && (
                    <div className="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-[#1a1a1a] px-4 py-3 text-sm text-white/65">
                        <Spinner className="size-4" />
                        Orion is thinking
                    </div>
                )}
            </div>

            <div className="border-t border-white/10 p-5">
                <form onSubmit={handleSubmit} className="space-y-3">
                    <label
                        htmlFor="orion-message"
                        className="block text-sm font-medium"
                    >
                        Message
                    </label>

                    <textarea
                        id="orion-message"
                        value={message}
                        onChange={(event) => onMessageChange(event.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder="Ask about a city, forecast, or what to wear."
                        className="min-h-28 w-full resize-none rounded-md border border-white/10 bg-[#0b0b0b] px-3 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-white/35 focus:border-[#f97316]"
                        disabled={isSending}
                    />

                    <InputError message={error} />

                    <div className="flex items-center justify-between gap-3">
                        <p className="text-sm text-white/55">
                            Press Enter to send. Use Shift+Enter for a new line.
                        </p>

                        <Button
                            type="submit"
                            disabled={isSending || !message.trim()}
                            className="min-w-28 bg-[#f97316] text-black hover:bg-[#fb8b3d]"
                        >
                            {isSending && <Spinner />}
                            Send
                        </Button>
                    </div>
                </form>
            </div>
        </section>
    );
}
