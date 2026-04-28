<?php

use Inertia\Testing\AssertableInertia as Assert;

test('welcome page does not expose registration props', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('welcome')
            ->missing('canRegister')
        );
});

test('landing component does not include faq or final call sections', function () {
    $component = file_get_contents(resource_path('js/components/orion-landing-content.tsx'));
    $content = file_get_contents(resource_path('js/components/orion-landing-content-data.ts'));
    $hook = file_get_contents(resource_path('js/hooks/use-orion-landing-animations.ts'));

    expect($component)
        ->toContain("from '@/components/orion-landing-content-data';")
        ->toContain("import { useOrionLandingAnimations } from '@/hooks/use-orion-landing-animations';")
        ->toContain('useOrionLandingAnimations(containerRef);')
        ->toContain('data-gsap="hero-title"')
        ->toContain('{heroContent.title}')
        ->toContain('{outcomesSection.title}')
        ->toContain('{testimonialsSection.title}')
        ->not->toContain("import { useGSAP } from '@gsap/react';")
        ->not->toContain("import gsap from 'gsap';")
        ->not->toContain("import { ScrollTrigger } from 'gsap/ScrollTrigger';")
        ->not->toContain('gsap.registerPlugin(useGSAP, ScrollTrigger);')
        ->not->toContain('ScrollTrigger.batch')
        ->not->toContain("filter: 'blur(10px)'")
        ->not->toContain('onEnterBack')
        ->not->toContain('onLeaveBack')
        ->not->toContain('prefers-reduced-motion: no-preference')
        ->not->toContain('const faqs = [')
        ->not->toContain('Final call')
        ->not->toContain('id="faq"')
        ->toContain('lg:min-h-[95vh]');

    expect($content)
        ->toContain('export const heroContent = {')
        ->toContain('export const practicalCreationSection = {')
        ->toContain('export const structureSection = {')
        ->toContain('export const conversionSection = {')
        ->toContain('export const outcomesSection = {')
        ->toContain('export const testimonialsSection = {')
        ->toContain('Generate videos for free from a simple prompt, then refine the result in seconds.')
        ->toContain('What improves when a free video generator is easier to understand at a glance.')
        ->toContain('The page now feels closer to a free video product creators would actually return to.')
        ->not->toContain('const faqs = [')
        ->not->toContain('Final call')
        ->not->toContain('id="faq"');

    expect($hook)
        ->toContain("import { useGSAP } from '@gsap/react';")
        ->toContain("import gsap from 'gsap';")
        ->toContain("import { ScrollTrigger } from 'gsap/ScrollTrigger';")
        ->toContain('gsap.registerPlugin(useGSAP, ScrollTrigger);')
        ->toContain('prefers-reduced-motion: no-preference')
        ->toContain('ScrollTrigger.batch')
        ->toContain("filter: 'blur(10px)'")
        ->toContain('onEnterBack')
        ->toContain('onLeaveBack')
        ->toContain('data-gsap="scroll-section"');
});

test('orion chat panel does not render nonce or video job debug labels', function () {
    $component = file_get_contents(resource_path('js/components/orion-chat-panel.tsx'));

    expect($component)
        ->not->toContain('Generate nonce:')
        ->not->toContain("Nonce:{' '}")
        ->not->toContain('Video job ID:')
        ->not->toContain('Result nonce:')
        ->not->toContain('Video URL:');
});
