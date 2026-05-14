export const promptSuggestions = [
    'Generate a free cinematic drone flyover of downtown Tokyo at blue hour with reflective streets and slow parallax.',
    'Create a free vertical product teaser for a matte black sneaker with macro shots, soft smoke, and hard rim lighting.',
    'Make a free surreal desert sequence with a lone traveler, floating fabric, and a slow push-in at golden hour.',
];

export const heroContent = {
    eyebrow: 'Free AI video generator',
    title: 'Generate videos for free from a simple prompt, then refine the result in seconds.',
    description:
        'Orion is a free digital product for creators, founders, and marketers who want fast AI video generation without expensive software, complex editing timelines, or a steep learning curve.',
    callout:
        'Built for free concept videos, product teasers, social clips, mood reels, and quick visual experiments before you commit to production.',
    previewEyebrow: 'Free generation preview',
    previewTitle:
        'One place to write the prompt, trigger the render, and watch the video come back.',
    primaryActionLabel: 'Start with a prompt',
};

export const practicalCreationSection = {
    eyebrow: 'Designed for practical creation',
    description:
        'Each section supports a real use case: explain what Orion generates for free, show who it helps, and lead the visitor toward trying their first prompt with confidence.',
};

export const featureCards = [
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

export const structureSection = {
    eyebrow: 'Structure',
    title: 'A free video generation product page that explains its value in seconds.',
    description:
        'Instead of generic AI filler, the page focuses on what creators actually care about: getting from idea to motion quickly, understanding that the tool is free, and seeing the result without unnecessary friction.',
    bullets: [
        'A clear hero that tells visitors they can generate AI videos for free right away',
        'Support sections that connect Orion to ads, storytelling, product launches, and social content',
        'A content system that stays easy to scan as more templates and free generation workflows are added',
    ],
};

export const conversionSection = {
    eyebrow: 'Main conversion block',
    title: 'Let people try a free video prompt before they commit to anything else.',
    description:
        'The quickest way to understand Orion is to try it: write a real scene, generate the first pass for free, and see how quickly an idea becomes a usable video concept.',
};
export const externalApiSection = {
    eyebrow: 'External API',
    title: 'Use the Veo endpoints directly from your own app, workflow, or automation.',
    description:
        'The website chat stays focused on the built-in experience, but the Veo routes are also available for external API use. Call them in sequence if you want step-by-step control, or use the combined endpoint when you want prompt-to-video URL in a single request.',
    flows: [
        {
            title: 'Step-by-step flow',
            items: [
                {
                    label: '1. Get a nonce',
                    endpoint: 'GET /veo/nonce',
                    sample: '{\n  "nonce": "nonce_abc123"\n}',
                },
                {
                    label: '2. Start generation',
                    endpoint: 'POST /veo/generate',
                    sample: '{\n  "nonce": "nonce_abc123",\n  "prompt": "A cinematic rainy city street at night."\n}',
                },
                {
                    label: '3. Fetch final video URL',
                    endpoint: 'POST /veo/video-url',
                    sample: '{\n  "nonce": "nonce_abc123",\n  "generate_video_id": "video_job_123"\n}',
                },
            ],
        },
        {
            title: 'Combined flow',
            items: [
                {
                    label: 'Single request for final URL',
                    endpoint: 'POST /veo/generate-video-url',
                    sample: '{\n  "nonce": "nonce_abc123",\n  "prompt": "A cinematic rainy city street at night."\n}',
                },
            ],
        },
    ],
    notes: [
        'Use the step-by-step flow when your client wants to manage generation state or poll for readiness itself.',
        'Use the combined flow when you want the backend to generate and poll for the final video URL on your behalf.',
        'The final `video_url` may still return `null` if Veo has not finished within the polling window.',
    ],
};

export const outcomesSection = {
    eyebrow: 'Outcomes',
    title: 'What improves when a free video generator is easier to understand at a glance.',
    description:
        'Better pacing builds confidence. Visitors instantly see that Orion is free, what kinds of videos it generates, and why writing a quick prompt is worth their time.',
    stats: [
        {
            value: '2-3x',
            description:
                'Clearer encouragement to try free cinematic prompts, product shots, and concept videos right away',
        },
        {
            value: '6-12 weeks',
            description:
                'Enough room to expand into templates, aspect ratios, shot controls, and creator-friendly premium upgrades later',
        },
        {
            value: '90%+',
            description:
                'Of the structure needed for a public-facing free AI video product page that already feels launch-ready',
        },
    ],
};

export const testimonialsSection = {
    eyebrow: 'Testimonials',
    title: 'The page now feels closer to a free video product creators would actually return to.',
    description:
        'The copy, structure, and prompt flow now support a more believable free-product experience instead of a generic AI page with video layered on top.',
    quotes: [
        'It feels like a real free video generator now, not just a chat box with a different label.',
        'The sample prompts make it obvious that Orion is for scenes, products, motion, and visual direction.',
        'The page explains why someone would try a free video generator before asking them to interact with it.',
    ],
};
