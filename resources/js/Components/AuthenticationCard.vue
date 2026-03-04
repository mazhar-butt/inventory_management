<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-dark">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-dark-400 via-dark-500 to-dark-600"></div>
            <div class="absolute inset-0 opacity-5">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, #E96012 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
        </div>
        
        <div class="relative z-10 w-full sm:max-w-md mt-6 px-8 py-8 bg-dark-300/80 backdrop-blur-sm shadow-2xl border border-dark-100/20 rounded-2xl">
            <slot />
        </div>
    </div>
</template>
