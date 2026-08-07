{{-- Content wrapper for all internal pages — fluid width with consistent padding --}}
<section {{ $attributes->class('mx-auto max-w-screen-2xl px-5 py-8 sm:px-7 lg:px-10 lg:py-10') }}>
    <div class="space-y-7">
        {{ $slot }}
    </div>
</section>
