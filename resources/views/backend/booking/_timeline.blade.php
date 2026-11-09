{{-- Booking history: messages sent to the guest + every change staff/system made. Needs $editData. --}}
@php
    $events = collect();
    foreach ($editData->messageLogs as $m) {
        $events->push(['at' => $m->sent_at ?? $m->created_at, 'icon' => $m->channel === 'whatsapp' ? 'bxl-whatsapp' : 'bx-envelope',
            'text' => ucfirst(str_replace('_', ' ', $m->template)) . ' ' . $m->channel . ' → ' . $m->recipient,
            'tone' => ['sent' => 'success', 'failed' => 'danger', 'skipped' => 'secondary'][$m->status] ?? 'warning', 'note' => $m->status . ($m->error ? ': ' . Str::limit($m->error, 120) : '')]);
    }
    foreach ($editData->auditLogs()->with('user')->limit(50)->get() as $a) {
        $fields = collect($a->changes ?? [])->except(['id'])->map(fn ($v, $k) => $k . ': ' . (is_array($v) ? (json_encode($v[0]) . ' → ' . json_encode($v[1])) : json_encode($v)))->take(6)->implode(' · ');
        $events->push(['at' => $a->created_at, 'icon' => 'bx-history',
            'text' => ($a->description ?: ucfirst($a->event)) . ' by ' . ($a->user->name ?? 'system/guest'),
            'tone' => 'secondary', 'note' => $a->event === 'created' ? '' : $fields]);
    }
    $events = $events->sortByDesc('at')->values();
@endphp
<div class="card mt-4">
    <div class="card-body">
        <h6 class="mb-3"><i class="bx bx-time-five"></i> Booking history</h6>
        @forelse ($events as $e)
            <div class="d-flex gap-2 mb-2 small">
                <i class="bx {{ $e['icon'] }} text-{{ $e['tone'] }} fs-5"></i>
                <div>
                    <div>{{ $e['text'] }} <span class="text-secondary">· {{ optional($e['at'])->format('d M Y H:i') }}</span></div>
                    @if ($e['note'])<div class="text-secondary text-break">{{ $e['note'] }}</div>@endif
                </div>
            </div>
        @empty
            <p class="text-secondary small mb-0">No history yet.</p>
        @endforelse
    </div>
</div>
