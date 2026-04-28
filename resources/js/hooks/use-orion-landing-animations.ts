import { useGSAP } from '@gsap/react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import type { RefObject } from 'react';

gsap.registerPlugin(useGSAP, ScrollTrigger);

export function useOrionLandingAnimations(
    containerRef: RefObject<HTMLDivElement | null>,
): void {
    useGSAP(
        () => {
            const media = gsap.matchMedia();

            media.add('(prefers-reduced-motion: no-preference)', () => {
                gsap.set('[data-gsap="float-shape"]', {
                    transformOrigin: 'center center',
                    willChange: 'transform',
                });

                gsap.set('[data-gsap="scroll-section"], [data-gsap="reveal-card"]', {
                    filter: 'blur(10px)',
                });

                const sharpenElements = (
                    targets: gsap.TweenTarget,
                    blurAmount = 0,
                ): void => {
                    gsap.to(targets, {
                        y: 0,
                        autoAlpha: 1,
                        filter: `blur(${blurAmount}px)`,
                        duration: 0.8,
                        ease: 'power2.out',
                        stagger: 0.1,
                        overwrite: true,
                    });
                };

                const blurElements = (
                    targets: gsap.TweenTarget,
                    y: number,
                ): void => {
                    gsap.to(targets, {
                        y,
                        autoAlpha: 0.38,
                        filter: 'blur(10px)',
                        duration: 0.55,
                        ease: 'power2.out',
                        stagger: 0.08,
                        overwrite: true,
                    });
                };

                const heroTimeline = gsap.timeline({
                    defaults: {
                        duration: 0.9,
                        ease: 'power3.out',
                    },
                });

                heroTimeline
                    .from('[data-gsap="hero-eyebrow"]', {
                        y: 18,
                        autoAlpha: 0,
                    })
                    .from(
                        '[data-gsap="hero-title"]',
                        {
                            y: 28,
                            autoAlpha: 0,
                        },
                        '-=0.55',
                    )
                    .from(
                        '[data-gsap="hero-body"]',
                        {
                            y: 24,
                            autoAlpha: 0,
                        },
                        '-=0.6',
                    )
                    .from(
                        '[data-gsap="hero-cta"]',
                        {
                            y: 22,
                            autoAlpha: 0,
                            stagger: 0.12,
                        },
                        '-=0.55',
                    )
                    .from(
                        '[data-gsap="hero-preview"]',
                        {
                            x: 28,
                            autoAlpha: 0,
                            duration: 1,
                        },
                        '-=0.95',
                    )
                    .from(
                        '[data-gsap="hero-preview-shape"]',
                        {
                            y: 24,
                            scale: 0.96,
                            autoAlpha: 0,
                            stagger: 0.08,
                            duration: 0.8,
                            ease: 'power2.out',
                        },
                        '-=0.7',
                    );

                gsap.to('[data-gsap="float-shape"]', {
                    y: (index) => (index % 2 === 0 ? -12 : -18),
                    duration: 2.8,
                    repeat: -1,
                    yoyo: true,
                    ease: 'sine.inOut',
                    stagger: 0.18,
                });

                gsap.from('[data-gsap="scroll-section"]', {
                    y: 36,
                    autoAlpha: 0,
                    filter: 'blur(10px)',
                    duration: 0.95,
                    ease: 'power2.out',
                    stagger: 0.14,
                    scrollTrigger: {
                        trigger: '[data-gsap="scroll-section-group"]',
                        start: 'top 72%',
                        onEnter: () => {
                            sharpenElements('[data-gsap="scroll-section"]');
                        },
                        onLeave: () => {
                            blurElements('[data-gsap="scroll-section"]', -22);
                        },
                        onEnterBack: () => {
                            sharpenElements('[data-gsap="scroll-section"]');
                        },
                        onLeaveBack: () => {
                            blurElements('[data-gsap="scroll-section"]', 22);
                        },
                    },
                });

                ScrollTrigger.batch('[data-gsap="reveal-card"]', {
                    start: 'top 82%',
                    onEnter: (elements) => {
                        sharpenElements(elements);
                    },
                    onLeave: (elements) => {
                        blurElements(elements, -18);
                    },
                    onEnterBack: (elements) => {
                        sharpenElements(elements);
                    },
                    onLeaveBack: (elements) => {
                        blurElements(elements, 18);
                    },
                });
            });

            return () => {
                media.revert();
            };
        },
        { scope: containerRef },
    );
}
