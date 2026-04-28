import { useRef } from 'react';
import type { ReactNode } from 'react';
import {
    conversionSection,
    featureCards,
    heroContent,
    outcomesSection,
    practicalCreationSection,
    promptSuggestions,
    structureSection,
    testimonialsSection,
} from '@/components/orion-landing-content-data';
import { useOrionLandingAnimations } from '@/hooks/use-orion-landing-animations';

type OrionLandingContentProps = {
    chatPanel: ReactNode;
    onSelectPrompt: (prompt: string) => void;
};

export default function OrionLandingContent({
    chatPanel,
    onSelectPrompt,
}: OrionLandingContentProps) {
    const containerRef = useRef<HTMLDivElement | null>(null);

    useOrionLandingAnimations(containerRef);

    return (
        <div ref={containerRef} id="top" className="bg-[#050505]">
            <section className="border-b border-white/10">
                <div className="mx-auto grid max-w-7xl gap-8 px-6 py-10 lg:min-h-[calc(100vh-4.5rem)] lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-center lg:px-10 lg:py-16">
                    <div className="flex flex-col justify-end gap-6 py-8 lg:py-12">
                        <div className="space-y-4">
                            <p
                                data-gsap="hero-eyebrow"
                                className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase"
                            >
                                {heroContent.eyebrow}
                            </p>
                            <h1
                                data-gsap="hero-title"
                                className="max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-balance lg:text-6xl"
                            >
                                {heroContent.title}
                            </h1>
                            <p
                                data-gsap="hero-body"
                                className="max-w-xl text-base leading-7 text-white/62 lg:text-lg"
                            >
                                {heroContent.description}
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <button
                                type="button"
                                onClick={() => onSelectPrompt(promptSuggestions[0])}
                                data-gsap="hero-cta"
                                className="rounded-md bg-[#f97316] px-4 py-2.5 text-sm font-medium text-black transition hover:brightness-110"
                            >
                                {heroContent.primaryActionLabel}
                            </button>
                            <p
                                data-gsap="hero-cta"
                                className="max-w-sm text-sm leading-6 text-white/45"
                            >
                                {heroContent.callout}
                            </p>
                        </div>
                    </div>

                    <div
                        data-gsap="hero-preview"
                        className="relative min-h-[26rem] overflow-hidden rounded-sm border border-white/10 bg-[#17110d] lg:min-h-[38rem]"
                    >
                        <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(249,115,22,0.35),_transparent_28%),linear-gradient(180deg,rgba(0,0,0,0.25),rgba(0,0,0,0.72))]" />
                        <div className="absolute inset-y-0 left-0 w-1/2 bg-[linear-gradient(180deg,rgba(255,255,255,0.06),transparent)]" />
                        <div className="absolute bottom-0 left-0 right-0 h-28 bg-black/45" />
                        <div
                            data-gsap="hero-preview-shape"
                            className="absolute left-[9%] top-[18%] h-48 w-28 rounded-t-[3rem] rounded-b-lg bg-[#36271e]/90 blur-[1px]"
                        />
                        <div
                            data-gsap="hero-preview-shape"
                            className="absolute left-[34%] top-[22%] h-56 w-32 rounded-t-[2.5rem] rounded-b-lg bg-[#4c392c]/90"
                        />
                        <div
                            data-gsap="hero-preview-shape"
                            className="absolute right-[12%] top-[16%] h-60 w-40 rounded-t-[3rem] rounded-b-lg bg-[#876347]/85"
                        />
                        <div
                            data-gsap="float-shape"
                            className="absolute bottom-[22%] left-[28%] h-3 w-32 rounded-full bg-[#c59e7b]/50 blur-sm"
                        />
                        <div
                            data-gsap="float-shape"
                            className="absolute bottom-[24%] right-[17%] h-3 w-28 rounded-full bg-[#d9b28f]/40 blur-sm"
                        />
                        <div data-gsap="hero-preview-shape" className="absolute bottom-6 left-6 max-w-sm">
                            <p className="text-xs font-medium tracking-[0.26em] text-white/45 uppercase">
                                {heroContent.previewEyebrow}
                            </p>
                            <p className="mt-3 text-2xl font-medium leading-tight text-white">
                                {heroContent.previewTitle}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section
                data-gsap="scroll-section-group"
                className="border-b border-white/10 bg-[#090909]"
            >
                <div className="mx-auto grid max-w-7xl gap-6 px-6 py-10 lg:min-h-[36rem] lg:grid-cols-4 lg:content-center lg:px-10">
                    <div data-gsap="scroll-section" className="lg:col-span-1">
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            {practicalCreationSection.eyebrow}
                        </p>
                        <p className="mt-3 max-w-xs text-sm leading-6 text-white/55">
                            {practicalCreationSection.description}
                        </p>
                    </div>
                    {featureCards.map((card) => (
                        <div
                            key={card.title}
                            data-gsap="reveal-card"
                            className="rounded-sm border border-white/10 bg-[#151515] p-6 lg:min-h-[15rem]"
                        >
                            <p className="text-sm font-medium text-white">
                                {card.title}
                            </p>
                            <p className="mt-3 text-sm leading-6 text-white/58">
                                {card.description}
                            </p>
                        </div>
                    ))}
                </div>
            </section>

            <section id="services" className="border-b border-white/10 bg-[#141414]">
                <div className="mx-auto grid max-w-7xl gap-10 px-6 py-12 lg:min-h-[85vh] lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] lg:items-center lg:px-10">
                    <div
                        data-gsap="scroll-section"
                        className="relative min-h-[24rem] overflow-hidden rounded-sm border border-white/10 bg-[#2a211b] lg:min-h-[34rem]"
                    >
                        <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(249,115,22,0.2),transparent_45%),linear-gradient(180deg,rgba(255,255,255,0.08),rgba(0,0,0,0.15))]" />
                        <div
                            data-gsap="float-shape"
                            className="absolute left-[13%] top-[18%] h-56 w-36 rounded-[1.75rem] bg-[#d4b89b]"
                        />
                        <div
                            data-gsap="float-shape"
                            className="absolute left-[42%] top-[14%] h-64 w-40 rounded-[1.75rem] bg-[#f1d9bf]"
                        />
                        <div className="absolute bottom-0 left-0 right-0 h-24 bg-black/25" />
                    </div>

                    <div
                        data-gsap="scroll-section"
                        className="flex flex-col justify-center gap-6"
                    >
                        <div>
                            <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                                {structureSection.eyebrow}
                            </p>
                            <h2 className="mt-3 max-w-lg text-3xl font-semibold leading-tight text-white lg:text-5xl">
                                {structureSection.title}
                            </h2>
                            <p className="mt-4 max-w-xl text-sm leading-7 text-white/58 lg:text-base">
                                {structureSection.description}
                            </p>
                        </div>

                        <div className="grid gap-4">
                            {structureSection.bullets.map((item) => (
                                <div
                                    key={item}
                                    className="flex items-start gap-3 border-b border-white/10 py-3"
                                >
                                    <div className="mt-1 h-2 w-2 rounded-full bg-[#f97316]" />
                                    <p className="text-sm leading-6 text-white/63">
                                        {item}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section className="border-b border-white/10 bg-[#050505]">
                <div className="mx-auto max-w-7xl px-6 py-12 lg:min-h-[95vh] lg:px-10 lg:py-16">
                    <div className="max-w-xl">
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            {conversionSection.eyebrow}
                        </p>
                        <h2 className="mt-3 max-w-2xl text-3xl font-semibold leading-tight text-white lg:text-5xl">
                            {conversionSection.title}
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            {conversionSection.description}
                        </p>
                    </div>

                    <div data-gsap="scroll-section" className="mt-8 lg:mt-12">
                        {chatPanel}
                    </div>
                </div>
            </section>

            <section className="border-b border-white/10 bg-[#171717]">
                <div className="mx-auto grid max-w-7xl gap-8 px-6 py-12 lg:min-h-[70vh] lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-center lg:px-10">
                    <div>
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            {outcomesSection.eyebrow}
                        </p>
                        <h2 className="mt-3 max-w-lg text-3xl font-semibold leading-tight text-white lg:text-5xl">
                            {outcomesSection.title}
                        </h2>
                        <p className="mt-4 max-w-md text-sm leading-7 text-white/58 lg:text-base">
                            {outcomesSection.description}
                        </p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-3">
                        {outcomesSection.stats.map((stat) => (
                            <div
                                key={stat.value}
                                data-gsap="reveal-card"
                                className="border-l border-white/10 pl-4"
                            >
                                <p className="text-3xl font-semibold text-white">
                                    {stat.value}
                                </p>
                                <p className="mt-3 text-sm leading-6 text-white/58">
                                    {stat.description}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            <section className="border-b border-white/10 bg-[#121212]">
                <div className="mx-auto max-w-7xl px-6 py-12 lg:min-h-[72vh] lg:px-10">
                    <div className="max-w-xl">
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            {testimonialsSection.eyebrow}
                        </p>
                        <h2 className="mt-3 max-w-2xl text-3xl font-semibold text-white lg:text-5xl">
                            {testimonialsSection.title}
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            {testimonialsSection.description}
                        </p>
                    </div>

                    <div className="mt-8 grid gap-5 lg:grid-cols-3">
                        {testimonialsSection.quotes.map((quote, index) => (
                            <div
                                key={quote}
                                data-gsap="reveal-card"
                                className="rounded-sm border border-white/10 bg-[#0d0d0d] p-5 lg:min-h-[16rem]"
                            >
                                <div className="mb-4 flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-medium text-black">
                                    {index + 1}
                                </div>
                                <p className="text-sm leading-6 text-white/68">
                                    {quote}
                                </p>
                            </div>
                        ))}
                    </div>
                </div>
            </section> 
        </div>
    );
}
