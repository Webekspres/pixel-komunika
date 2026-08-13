import * as lucide from 'lucide';

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

document.addEventListener('DOMContentLoaded', refreshIcons);
document.addEventListener('livewire:navigated', refreshIcons);
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el }) => {
        if (el?.querySelector?.('[data-lucide], [data-lucide] *') || el?.hasAttribute?.('data-lucide')) {
            refreshIcons();
        }
    });
});
