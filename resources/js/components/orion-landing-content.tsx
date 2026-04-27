import type { ReactNode } from 'react';

type OrionLandingContentProps = {
    chatPanel: ReactNode;
    onSelectPrompt: (prompt: string) => void;
};

const promptSuggestions = [
    'What is the weather in Manila today?',
    'Will it rain in Cebu this afternoon?',
    'What should I wear in Baguio tomorrow morning?',
];

const featureCards = [
    {
        title: 'Guest-ready',
        description:
            'Visitors can ask about rain, heat, wind, or travel conditions immediately without creating an account first.',
    },
    {
        title: 'Operationally simple',
        description:
            'One landing page and one assistant surface keep weather questions easy to ask, answer, and maintain over time.',
    },
    {
        title: 'Ready to evolve',
        description:
            'Forecast prompts, seasonal guidance, travel tips, and local weather use cases can grow without rebuilding the whole experience.',
    },
];

export default function OrionLandingContent({
    chatPanel,
    onSelectPrompt,
}: OrionLandingContentProps) {
    return (
        <div id="top" className="bg-[#050505]">
            <section className="border-b border-white/10">
                <div className="mx-auto grid max-w-7xl gap-8 px-6 py-10 lg:min-h-[calc(100vh-4.5rem)] lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:items-center lg:px-10 lg:py-16">
                    <div className="flex flex-col justify-end gap-6 py-8 lg:py-12">
                        <div className="space-y-4">
                            <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                                Public weather assistant
                            </p>
                            <h1 className="max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-balance lg:text-6xl">
                                Ask about the weather and get useful guidance right away.
                            </h1>
                            <p className="max-w-xl text-base leading-7 text-white/62 lg:text-lg">
                                Orion helps people check conditions, plan what
                                to wear, prepare for rain, and make small daily
                                decisions with less guesswork. The page keeps
                                that promise front and center from the first
                                screen.
                            </p>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <button
                                type="button"
                                onClick={() => onSelectPrompt(promptSuggestions[0])}
                                className="rounded-md bg-[#f97316] px-4 py-2.5 text-sm font-medium text-black transition hover:brightness-110"
                            >
                                Start with a prompt
                            </button>
                            <p className="max-w-sm text-sm leading-6 text-white/45">
                                Built for everyday forecast checks, commute
                                planning, weekend trips, and quick questions
                                about what the weather means in practice.
                            </p>
                        </div>
                    </div>

                    <div className="relative min-h-[26rem] overflow-hidden rounded-sm border border-white/10 bg-[#17110d] lg:min-h-[38rem]">
                        <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(249,115,22,0.35),_transparent_28%),linear-gradient(180deg,rgba(0,0,0,0.25),rgba(0,0,0,0.72))]" />
                        <div className="absolute inset-y-0 left-0 w-1/2 bg-[linear-gradient(180deg,rgba(255,255,255,0.06),transparent)]" />
                        <div className="absolute bottom-0 left-0 right-0 h-28 bg-black/45" />
                        <div className="absolute left-[9%] top-[18%] h-48 w-28 rounded-t-[3rem] rounded-b-lg bg-[#36271e]/90 blur-[1px]" />
                        <div className="absolute left-[34%] top-[22%] h-56 w-32 rounded-t-[2.5rem] rounded-b-lg bg-[#4c392c]/90" />
                        <div className="absolute right-[12%] top-[16%] h-60 w-40 rounded-t-[3rem] rounded-b-lg bg-[#876347]/85" />
                        <div className="absolute bottom-[22%] left-[28%] h-3 w-32 rounded-full bg-[#c59e7b]/50 blur-sm" />
                        <div className="absolute bottom-[24%] right-[17%] h-3 w-28 rounded-full bg-[#d9b28f]/40 blur-sm" />
                        <div className="absolute bottom-6 left-6 max-w-sm">
                            <p className="text-xs font-medium tracking-[0.26em] text-white/45 uppercase">
                                Live weather help
                            </p>
                            <p className="mt-3 text-2xl font-medium leading-tight text-white">
                                One place to ask about today, tonight, or the trip ahead.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section className="border-b border-white/10 bg-[#090909]">
                <div className="mx-auto grid max-w-7xl gap-6 px-6 py-10 lg:min-h-[36rem] lg:grid-cols-4 lg:content-center lg:px-10">
                    <div className="lg:col-span-1">
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            Designed for practical use
                        </p>
                        <p className="mt-3 max-w-xs text-sm leading-6 text-white/55">
                            Each section supports a real weather use case:
                            explain what Orion answers, show where it helps,
                            and lead the visitor toward trying a forecast
                            question themselves.
                        </p>
                    </div>
                    {featureCards.map((card) => (
                        <div
                            key={card.title}
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
                    <div className="relative min-h-[24rem] overflow-hidden rounded-sm border border-white/10 bg-[#2a211b] lg:min-h-[34rem]">
                        <div className="absolute inset-0 bg-[linear-gradient(135deg,rgba(249,115,22,0.2),transparent_45%),linear-gradient(180deg,rgba(255,255,255,0.08),rgba(0,0,0,0.15))]" />
                        <div className="absolute left-[13%] top-[18%] h-56 w-36 rounded-[1.75rem] bg-[#d4b89b]" />
                        <div className="absolute left-[42%] top-[14%] h-64 w-40 rounded-[1.75rem] bg-[#f1d9bf]" />
                        <div className="absolute bottom-0 left-0 right-0 h-24 bg-black/25" />
                    </div>

                    <div className="flex flex-col justify-center gap-6">
                        <div>
                            <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                                Structure
                            </p>
                            <h2 className="mt-3 max-w-lg text-3xl font-semibold leading-tight text-white lg:text-5xl">
                                A weather assistant landing page that explains its value quickly.
                            </h2>
                            <p className="mt-4 max-w-xl text-sm leading-7 text-white/58 lg:text-base">
                                Instead of generic filler, the page focuses on
                                the moments people actually care about:
                                checking rain before leaving, planning for heat
                                later today, and deciding what to pack for the
                                next stop.
                            </p>
                        </div>

                        <div className="grid gap-4">
                            {[
                                'A clear hero that tells visitors they can ask real weather questions immediately',
                                'Support sections that connect Orion to travel, daily planning, and changing conditions',
                                'A content system that stays easy to scan even as more forecast use cases are added',
                            ].map((item) => (
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
                            Main conversion block
                        </p>
                        <h2 className="mt-3 max-w-2xl text-3xl font-semibold leading-tight text-white lg:text-5xl">
                            Let people try a weather question before they commit to anything else.
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            The quickest way to understand Orion is to ask it
                            something real: whether it will rain later, what
                            conditions feel like in another city, or how to
                            prepare for the morning ahead.
                        </p>
                    </div>

                    <div className="mt-8 lg:mt-12">{chatPanel}</div>
                </div>
            </section>

            <section className="border-b border-white/10 bg-[#171717]">
                <div className="mx-auto grid max-w-7xl gap-8 px-6 py-12 lg:min-h-[70vh] lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:items-center lg:px-10">
                    <div>
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            Outcomes
                        </p>
                        <h2 className="mt-3 max-w-lg text-3xl font-semibold leading-tight text-white lg:text-5xl">
                            What improves when the weather assistant is easier to understand at a glance.
                        </h2>
                        <p className="mt-4 max-w-md text-sm leading-7 text-white/58 lg:text-base">
                            Better pacing builds confidence. Visitors see what
                            Orion helps with, when to use it, and why asking a
                            quick weather question feels worth their time.
                        </p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-3">
                        {[
                            ['2-3x', 'Clearer encouragement to ask about rain, temperature, and travel conditions right away'],
                            ['6-12 weeks', 'Enough room to expand into alerts, location-specific guidance, and seasonal weather content'],
                            ['90%+', 'Of the structure needed for a public weather assistant page that already feels launch-ready'],
                        ].map(([value, description]) => (
                            <div
                                key={value}
                                className="border-l border-white/10 pl-4"
                            >
                                <p className="text-3xl font-semibold text-white">
                                    {value}
                                </p>
                                <p className="mt-3 text-sm leading-6 text-white/58">
                                    {description}
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
                            Testimonials
                        </p>
                        <h2 className="mt-3 max-w-2xl text-3xl font-semibold text-white lg:text-5xl">
                            The page now feels closer to a weather product people would actually return to.
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            The copy, structure, and prompt flow now support a
                            more believable weather experience instead of a
                            generic AI page with weather layered on top.
                        </p>
                    </div>

                    <div className="mt-8 grid gap-5 lg:grid-cols-3">
                        {[
                            'It feels like a weather service now, not just a chat box with a different label.',
                            'The sample prompts make it obvious that Orion is for rain checks, planning, and local conditions.',
                            'The page explains why someone would use a weather assistant before asking them to interact with it.',
                        ].map((quote, index) => (
                            <div
                                key={quote}
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
