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
                    <p className="text-sm font-medium">Prompt Console</p>
                    <p className="text-sm text-white/55">
                        Write a scene, refine the direction, and Orion will turn it into a free video generation request.
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
                {messages.map((chatMessage: ChatMessage) => (
                    <div
                        key={chatMessage.id}
                        className={
                            chatMessage.role === 'user'
                                ? 'ml-auto max-w-[85%] rounded-lg bg-[#f97316] px-4 py-3 text-sm leading-6 text-black'
                                : 'max-w-[85%] rounded-lg border border-white/10 bg-[#1a1a1a] px-4 py-3 text-sm leading-6 text-[#f5f2ea]'
                        }
                    >
                        <p>{chatMessage.text}</p>
                        {chatMessage.role === 'assistant' &&
                        (chatMessage.videoStatus === 'processing' ||
                            chatMessage.videoStatus === 'failed' ||
                            Boolean(chatMessage.videoUrl)) ? (
                            <div className="mt-3 space-y-1 border-t border-white/10 pt-3 text-xs text-white/55">
                                {chatMessage.videoStatus === 'processing' ? (
                                    <div className="mb-3 overflow-hidden rounded-lg border border-white/10 bg-[#101010]">
                                        <div className="relative aspect-[9/16] w-full max-w-[15rem] bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.18),transparent_40%),linear-gradient(180deg,#181818,#0b0b0b)]">
                                            <div className="absolute inset-0 bg-[linear-gradient(110deg,transparent,rgba(255,255,255,0.08),transparent)] animate-pulse" />
                                            <div className="absolute inset-x-0 top-0 h-12 bg-gradient-to-b from-black/35 to-transparent" />
                                            <div className="absolute inset-x-4 top-4 flex items-center justify-between text-[11px] uppercase tracking-[0.2em] text-white/55">
                                                <span>Generating</span>
                                                <Spinner className="size-3.5" />
                                            </div>
                                            <div className="absolute inset-x-4 bottom-4 space-y-3">
                                                <div className="h-2.5 w-3/4 rounded-full bg-white/12" />
                                                <div className="h-2.5 w-1/2 rounded-full bg-white/10" />
                                                <div className="flex items-center gap-2 text-[11px] text-white/50">
                                                    <span className="inline-flex h-6 w-6 items-center justify-center rounded-full border border-white/10 bg-black/30">
                                                        <Spinner className="size-3" />
                                                    </span>
                                                    <span>Rendering video preview...</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="border-t border-white/10 px-3 py-2 text-xs text-white/60">
                                            Orion will attach the finished video here as soon as the free render is ready.
                                        </div>
                                    </div>
                                ) : null}
                                {chatMessage.videoStatus === 'failed' && chatMessage.videoError ? (
                                    <p className="text-amber-300">
                                        {chatMessage.videoError}
                                    </p>
                                ) : null}
                                {chatMessage.videoUrl ? (
                                    <div className="space-y-3 pt-2">
                                        <video
                                            src={chatMessage.videoUrl}
                                            controls
                                            playsInline
                                            preload="metadata"
                                            className="aspect-[9/16] w-full max-w-[15rem] rounded-lg border border-white/10 bg-black"
                                        />
                                        <a
                                            href={chatMessage.videoUrl}
                                            target="_blank"
                                            rel="noreferrer"
                                            className="inline-flex text-[#f97316] transition hover:text-[#fb8b3d]"
                                        >
                                            Open video in new tab
                                        </a>
                                    </div>
                                ) : null}
                            </div>
                        ) : null}
                    </div>
                ))}

                {isSending && (
                    <div className="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-[#1a1a1a] px-4 py-3 text-sm text-white/65">
                        <Spinner className="size-4" />
                        Orion is building your next shot
                    </div>
                )}
            </div>

            <div className="border-t border-white/10 p-5">
                <form onSubmit={handleSubmit} className="space-y-3">
                    <label
                        htmlFor="orion-message"
                        className="block text-sm font-medium"
                    >
                        Free video prompt
                    </label>

                    <textarea
                        id="orion-message"
                        value={message}
                        onChange={(event) => onMessageChange(event.target.value)}
                        onKeyDown={handleKeyDown}
                        placeholder="Describe the scene, camera movement, mood, style, and format you want to generate for free."
                        className="min-h-28 w-full resize-none rounded-md border border-white/10 bg-[#0b0b0b] px-3 py-3 text-sm leading-6 text-white outline-none transition placeholder:text-white/35 focus:border-[#f97316]"
                        disabled={isSending}
                    />

                    <InputError message={error} />

                    <div className="flex items-center justify-between gap-3">
                        <p className="text-sm text-white/55">
                            Press Enter to generate. Use Shift+Enter for a new line.
                        </p>

                        <Button
                            type="submit"
                            disabled={isSending || !message.trim()}
                            className="min-w-28 bg-[#f97316] text-black hover:bg-[#fb8b3d]"
                        >
                            {isSending && <Spinner />}
                            Generate
                        </Button>
                    </div>
                </form>
            </div>
        </section>
    );
}
