@props([
    'title',
    'icon',
    'count',
    'percentageChange' => null, // Changed to null to detect when no data
    'changeDirection' => null  // Changed to null to detect when no data
])

<div {{ $attributes->merge(['class' => 'card border-light p-1 mt-3 flex-fill border-1 border-light-subtle', 'style' => 'min-width: 200px; max-width: 300px;']) }}>
    <div class="card-body d-flex flex-column">
        <div {{ $attributes->merge(['class' => 'text-muted mb-2 small']) }}>{{ $title }}</div>
        <div class="d-flex justify-content-between align-items-center flex-grow-1">
            <div {{ $attributes->merge(['class' => 'h4 fw-bold mb-0 icon']) }} style="line-height: 1; flex: 1; min-width: 0;">
                {{ $count }}
            </div>
            <div class="p-1 flex-shrink-0" style="max-width: 40px; max-height: 40px; overflow: hidden;">
                <div class="card-icon" style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                    {!! $icon !!}
                </div>
            </div>
        </div>
        {{-- Only show percentage change if we have real data --}}
        @if (is_numeric($percentageChange) && $changeDirection !== null && ($percentageChange > 0 || $changeDirection !== 'neutral'))
            <div class="mt-1">
                <small class="metric-change {{ $changeDirection === 'up' ? 'text-success' : ($changeDirection === 'down' ? 'text-danger' : 'text-muted') }}" style="font-size: 0.875rem; opacity: 0.9;">
                    @if ($changeDirection === 'up')
                        <i class="fas fa-arrow-up me-1"></i>
                    @elseif ($changeDirection === 'down')
                        <i class="fas fa-arrow-down me-1"></i>
                    @endif
                    {{ number_format($percentageChange, 1) }}%
                    @if ($changeDirection !== 'neutral')
                        from last month
                    @endif
                </small>
            </div>
        @elseif($percentageChange === 0 && $changeDirection === 'neutral')
            <div class="mt-1">
                <small class="text-muted" style="font-size: 0.875rem; opacity: 0.7;">
                    No change from last month
                </small>
            </div>
        @else
            <div class="mt-1">
                <small class="text-muted" style="font-size: 0.875rem; opacity: 0.7;">
                    Start creating content to see trends
                </small>
            </div>
        @endif
    </div>
</div>