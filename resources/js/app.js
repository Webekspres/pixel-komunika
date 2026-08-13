import * as lucide from 'lucide';
import { destroyStorefrontMotion, initStorefrontMotion } from './storefront-motion';

const { createIcons, icons: lucideIcons } = lucide;

// lucide@1.x: named exports are iconNode arrays; prefer the icons namespace.
const icons =
    lucideIcons && Object.keys(lucideIcons).length > 0
        ? lucideIcons
        : Object.fromEntries(
              Object.entries(lucide).filter(
                  ([key, value]) =>
                      typeof value !== 'function' &&
                      key !== 'icons' &&
                      Array.isArray(value) &&
                      value.length > 0 &&
                      Array.isArray(value[0]),
              ),
          );

const fallbackIconName = 'circle-alert';

function toExportName(name) {
    return name
        .split('-')
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join('');
}

function refreshIcons() {
    document.querySelectorAll('[data-lucide]').forEach((element) => {
        const iconName = element.getAttribute('data-lucide');
        const exportName = toExportName(iconName ?? '');

        if (!icons[exportName]) {
            console.warn(`[lucide] Missing icon "${iconName}", falling back to "${fallbackIconName}".`);
            element.setAttribute('data-lucide', fallbackIconName);
        }
    });

    createIcons({ icons });
}

function bootUi(root = document) {
    refreshIcons();
    destroyStorefrontMotion();
    // After full remount (navigate), clear done flags so motion can re-run.
    if (root === document || root === document.documentElement || root === document.body) {
        document.querySelectorAll('[data-reveal-done], [data-hero-done]').forEach((el) => {
            el.removeAttribute('data-reveal-done');
            el.removeAttribute('data-hero-done');
        });
    }
    initStorefrontMotion(root);
}

document.addEventListener('DOMContentLoaded', () => bootUi());
document.addEventListener('livewire:navigated', () => bootUi());
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => {
        if (el?.querySelector?.('[data-lucide], [data-lucide] *') || el?.hasAttribute?.('data-lucide')) {
            refreshIcons();
        }
        if (el?.querySelector?.('[data-reveal], [data-hero-animate]') || el?.hasAttribute?.('data-reveal') || el?.hasAttribute?.('data-hero-animate')) {
            // Morph only: init new nodes without killing existing scroll triggers.
            initStorefrontMotion(el);
        }
    });
});
