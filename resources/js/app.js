import {
    createIcons,
    BadgeCheck,
    BatteryCharging,
    Cable,
    Headphones,
    Package,
    Radio,
    Search,
    ShoppingCart,
    Smartphone,
    Truck,
} from 'lucide';

const icons = {
    BadgeCheck,
    BatteryCharging,
    Cable,
    Headphones,
    Package,
    Radio,
    Search,
    ShoppingCart,
    Smartphone,
    Truck,
};

function refreshIcons() {
    createIcons({ icons });
}

document.addEventListener('DOMContentLoaded', refreshIcons);
document.addEventListener('livewire:navigated', refreshIcons);
