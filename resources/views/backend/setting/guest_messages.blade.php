@extends('admin.admin_dashboard')
@section('admin')
<div class="page-content">
    <div class="page-breadcrumb d-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Guest messages</div>
        <div class="ms-auto"><a href="{{ route('system.messages') }}" class="btn btn-sm btn-outline-primary"><i class="bx bx-list-ul"></i> Message log</a></div>
    </div>

    <p class="text-secondary">Emails go out in the background. Each one is sent once per booking. Placeholders you can use:
        <code>{guest}</code> <code>{room}</code> <code>{check_in}</code> <code>{check_out}</code> <code>{nights}</code> <code>{guests}</code>
        <code>{total}</code> <code>{payment}</code> <code>{code}</code> <code>{summary}</code> <code>{hotel}</code> <code>{hotel_phone}</code> <code>{hotel_address}</code>.
        Emails need working SMTP (Settings → SMTP Setting).</p>

    <form method="POST" action="{{ route('guest.messages.update') }}">
        @csrf
        <div class="card"><div class="card-body">
            <h6><i class="bx bxl-whatsapp text-success"></i> WhatsApp</h6>
            <div class="row g-2 align-items-center">
                <div class="col-md-4"><label for="wa" class="form-label mb-0">Hotel WhatsApp number</label></div>
                <div class="col-md-5"><input id="wa" type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $site->whatsapp_number) }}" placeholder="{{ $site->phone ?: '0771 234 567' }}"></div>
                <div class="col-md-3 small text-secondary">Used for the “Chat on WhatsApp” button. Empty = hotel phone.</div>
            </div>
        </div></div>

        @foreach ($templates as $t)
            <div class="card mt-3"><div class="card-body">
                <div class="d-flex align-items-start gap-3 flex-wrap">
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" role="switch" id="en-{{ $t['key'] }}" name="messages[{{ $t['key'] }}][enabled]" value="1" @checked($t['enabled'])>
                        <label class="form-check-label fw-semibold" for="en-{{ $t['key'] }}">{{ $t['label'] }}</label>
                    </div>
                    <div class="small text-secondary flex-grow-1 mt-1">{{ $t['when'] }} · to {{ $t['to'] === 'hotel' ? 'the hotel email (Site Setting)' : 'the guest' }}</div>
                    <button type="submit" form="test-{{ $t['key'] }}" class="btn btn-sm btn-outline-secondary"><i class="bx bx-send"></i> Send test to me</button>
                </div>
                <label class="form-label mt-3 mb-1 small" for="sub-{{ $t['key'] }}">Subject</label>
                <input id="sub-{{ $t['key'] }}" type="text" class="form-control" name="messages[{{ $t['key'] }}][subject]" value="{{ $t['subject'] }}">
                <label class="form-label mt-2 mb-1 small" for="body-{{ $t['key'] }}">Message</label>
                <textarea id="body-{{ $t['key'] }}" class="form-control" rows="6" name="messages[{{ $t['key'] }}][body]">{{ $t['body'] }}</textarea>
            </div></div>
        @endforeach

        <div class="mt-3 mb-4"><button type="submit" class="btn btn-primary px-4">Save guest messages</button></div>
    </form>

    @foreach ($templates as $t)
        <form id="test-{{ $t['key'] }}" method="POST" action="{{ route('guest.messages.test', $t['key']) }}" class="d-none">@csrf</form>
    @endforeach
</div>
@endsection
