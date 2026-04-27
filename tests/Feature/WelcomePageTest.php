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

    expect($component)
        ->not->toContain('const faqs = [')
        ->not->toContain('Final call')
        ->not->toContain('id="faq"')
        ->toContain('Generate videos for free from a simple prompt, then refine the result in seconds.')
        ->toContain('lg:min-h-[95vh]');
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
