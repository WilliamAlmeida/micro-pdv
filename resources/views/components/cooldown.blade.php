@props([
    'count' => 2,
    'interval' => 1000,
    'amount' => 1,
])

@if($count > 0)
    <div {{ $attributes->merge(['class' => 'px-1 select-none']) }} x-data="{
        count: {{ $count }},
        timer: null,
        startCountdown() {
            this.timer = setInterval(() => {
                this.count -= parseFloat({{ $amount }});
                this.count = this.count.toFixed({{ !is_float($amount) ? 1 : 0 }});
                if (parseFloat(this.count) < 0) {
                    clearInterval(this.timer);
                    this.count = {{ $count }};
                    this.startCountdown();
                }
            }, {{ $interval }});
        },
        stopCountdown() {
            clearInterval(this.timer);
        },
        restartCountdown() {
            this.count = {{ $count }};
            this.stopCountdown();
            this.startCountdown();
        }
    }" x-init="startCountdown()">
        <template x-if="count >= 0">
            <span x-text="count"></span>
        </template>
        <template x-else>
            <span x-text="count"></span>
        </template>
    </div>
@endif