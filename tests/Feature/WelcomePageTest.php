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
        ->toContain('Ask about the weather and get useful guidance right away.')
        ->toContain('lg:min-h-[95vh]');
});
