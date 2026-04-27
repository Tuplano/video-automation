import type { ReactNode } from 'react';

type OrionLandingContentProps = {
    chatPanel: ReactNode;
    onSelectPrompt: (prompt: string) => void;
};

const promptSuggestions = [
    'Generate a free cinematic drone flyover of downtown Tokyo at blue hour with reflective streets and slow parallax.',
    'Create a free vertical product teaser for a matte black sneaker with macro shots, soft smoke, and hard rim lighting.',
    'Make a free surreal desert sequence with a lone traveler, floating fabric, and a slow push-in at golden hour.',
];

const featureCards = [
    {
        title: 'Free to start',
        description:
            'Visitors can start generating AI videos immediately without paying, signing up, or learning a complicated workflow.',
    },
    {
        title: 'Digital product',
        description:
            'One focused digital product keeps ideation, prompting, previewing, and final playback in a single streamlined experience.',
    },
    {
        title: 'Made for creators',
        description:
            'Style presets, shot variations, creator workflows, and branded directions can grow as the product expands beyond its free entry point.',
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
                                Free AI video generator
                            </p>
                            <h1 className="max-w-2xl text-4xl font-semibold leading-tight tracking-tight text-balance lg:text-6xl">
                                Generate videos for free from a simple prompt, then refine the result in seconds.
                            </h1>
                            <p className="max-w-xl text-base leading-7 text-white/62 lg:text-lg">
                                Orion is a free digital product for creators,
                                founders, and marketers who want fast AI video
                                generation without expensive software, complex
                                editing timelines, or a steep learning curve.
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
                                Built for free concept videos, product teasers,
                                social clips, mood reels, and quick visual
                                experiments before you commit to production.
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
                                Free generation preview
                            </p>
                            <p className="mt-3 text-2xl font-medium leading-tight text-white">
                                One place to write the prompt, trigger the render, and watch the video come back.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section className="border-b border-white/10 bg-[#090909]">
                <div className="mx-auto grid max-w-7xl gap-6 px-6 py-10 lg:min-h-[36rem] lg:grid-cols-4 lg:content-center lg:px-10">
                    <div className="lg:col-span-1">
                        <p className="text-xs font-medium tracking-[0.28em] text-[#f97316] uppercase">
                            Designed for practical creation
                        </p>
                        <p className="mt-3 max-w-xs text-sm leading-6 text-white/55">
                            Each section supports a real use case: explain
                            what Orion generates for free, show who it helps,
                            and lead the visitor toward trying their first
                            prompt with confidence.
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
                                A free video generation product page that explains its value in seconds.
                            </h2>
                            <p className="mt-4 max-w-xl text-sm leading-7 text-white/58 lg:text-base">
                                Instead of generic AI filler, the page focuses
                                on what creators actually care about: getting
                                from idea to motion quickly, understanding that
                                the tool is free, and seeing the result without
                                unnecessary friction.
                            </p>
                        </div>

                        <div className="grid gap-4">
                            {[
                                'A clear hero that tells visitors they can generate AI videos for free right away',
                                'Support sections that connect Orion to ads, storytelling, product launches, and social content',
                                'A content system that stays easy to scan as more templates and free generation workflows are added',
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
                            Let people try a free video prompt before they commit to anything else.
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            The quickest way to understand Orion is to try it:
                            write a real scene, generate the first pass for
                            free, and see how quickly an idea becomes a usable
                            video concept.
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
                            What improves when a free video generator is easier to understand at a glance.
                        </h2>
                        <p className="mt-4 max-w-md text-sm leading-7 text-white/58 lg:text-base">
                            Better pacing builds confidence. Visitors instantly
                            see that Orion is free, what kinds of videos it
                            generates, and why writing a quick prompt is worth
                            their time.
                        </p>
                    </div>

                    <div className="grid gap-6 sm:grid-cols-3">
                        {[
                            ['2-3x', 'Clearer encouragement to try free cinematic prompts, product shots, and concept videos right away'],
                            ['6-12 weeks', 'Enough room to expand into templates, aspect ratios, shot controls, and creator-friendly premium upgrades later'],
                            ['90%+', 'Of the structure needed for a public-facing free AI video product page that already feels launch-ready'],
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
                            The page now feels closer to a free video product creators would actually return to.
                        </h2>
                        <p className="mt-4 max-w-2xl text-sm leading-7 text-white/58 lg:text-base">
                            The copy, structure, and prompt flow now support a
                            more believable free-product experience instead of
                            a generic AI page with video layered on top.
                        </p>
                    </div>

                    <div className="mt-8 grid gap-5 lg:grid-cols-3">
                        {[
                            'It feels like a real free video generator now, not just a chat box with a different label.',
                            'The sample prompts make it obvious that Orion is for scenes, products, motion, and visual direction.',
                            'The page explains why someone would try a free video generator before asking them to interact with it.',
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
