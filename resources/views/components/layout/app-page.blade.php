{{-- Content wrapper for all customer-facing pages — fluid width with controlled rhythm --}}
<section {{ $attributes->class('container-2xl py-6 sm:py-8 lg:py-10') }}>
    <div class="space-y-6 sm:space-y-7">
        {{ $slot }}
    </div>
</section>
