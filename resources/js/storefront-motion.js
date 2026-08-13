import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const REVEAL_DONE = 'data-reveal-done';
const HERO_DONE = 'data-hero-done';

/** @type {ScrollTrigger[]} */
let managedTriggers = [];
/** @type {gsap.core.Timeline | null} */
let heroTimeline = null;

function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

function parseRevealDelay(el) {
    const raw = getComputedStyle(el).getPropertyValue('--reveal-delay').trim();
    if (!raw) {
        return 0;
    }
    const ms = Number.parseFloat(raw);
    if (Number.isNaN(ms)) {
        return 0;
    }
    return raw.endsWith('s') && !raw.endsWith('ms') ? ms : ms / 1000;
}

function revealFromVars(el) {
    const mode = el.getAttribute('data-reveal');
    const vars = { autoAlpha: 0, y: 18 };

    if (mode === 'scale') {
        delete vars.y;
        vars.scale = 0.96;
    } else if (mode === 'left') {
        delete vars.y;
        vars.x = -16;
    }

    return vars;
}

function markDone(el, attr) {
    el.setAttribute(attr, '');
}

function clearInlineMotion(el) {
    gsap.set(el, { clearProps: 'opacity,visibility,transform' });
}

/**
 * Kill hero timeline + ScrollTriggers created by this module.
 */
export function destroyStorefrontMotion() {
    if (heroTimeline) {
        heroTimeline.kill();
        heroTimeline = null;
    }

    managedTriggers.forEach((trigger) => trigger.kill());
    managedTriggers = [];
}

function initHero(root) {
    const scope = root instanceof Element ? root : document;
    const items = [...scope.querySelectorAll(`[data-hero-animate]:not([${HERO_DONE}])`)];
    if (!items.length) {
        return;
    }

    if (prefersReducedMotion()) {
        items.forEach((el) => {
            clearInlineMotion(el);
            markDone(el, HERO_DONE);
        });
        return;
    }

    gsap.set(items, { autoAlpha: 0, y: 18 });
    heroTimeline = gsap.timeline({ defaults: { ease: 'power2.out', duration: 0.55 } });
    heroTimeline.to(items, {
        autoAlpha: 1,
        y: 0,
        stagger: 0.1,
        onComplete: () => items.forEach((el) => markDone(el, HERO_DONE)),
    });
}

function initReveals(root) {
    const scope = root instanceof Element ? root : document;
    const nodes = [...scope.querySelectorAll(`[data-reveal]:not([${REVEAL_DONE}])`)];
    if (!nodes.length) {
        return;
    }

    if (prefersReducedMotion()) {
        nodes.forEach((el) => {
            clearInlineMotion(el);
            markDone(el, REVEAL_DONE);
        });
        return;
    }

    nodes.forEach((el) => {
        const from = revealFromVars(el);
        const delay = parseRevealDelay(el);

        gsap.set(el, from);

        const tween = gsap.to(el, {
            autoAlpha: 1,
            x: 0,
            y: 0,
            scale: 1,
            duration: 0.65,
            delay,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 88%',
                once: true,
            },
            onComplete: () => markDone(el, REVEAL_DONE),
        });

        if (tween.scrollTrigger) {
            managedTriggers.push(tween.scrollTrigger);
        }
    });
}

/**
 * Boot / refresh storefront motion for a document or subtree.
 * @param {ParentNode} [root=document]
 */
export function initStorefrontMotion(root = document) {
    initHero(root);
    initReveals(root);
    ScrollTrigger.refresh();
}
