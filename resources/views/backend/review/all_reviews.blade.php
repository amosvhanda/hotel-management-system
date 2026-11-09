@extends('admin.admin_dashboard')
@section('admin')

<div class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Guest Reviews</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                </ol>
            </nav>
        </div>
    </div>

    <p class="text-secondary mb-3">Guests rate their stay from <em>My Bookings</em> after check-out. Published reviews show on the room page, and 4–5★ reviews also show on the homepage.</p>

    <div class="btn-group mb-3 flex-wrap" role="group" aria-label="Filter reviews">
        @foreach (['pending' => 'Waiting for approval', 'approved' => 'Published', 'hidden' => 'Hidden', 'all' => 'All'] as $key => $label)
            <a href="{{ route('all.reviews', $key === 'all' ? [] : ['status' => $key]) }}"
               class="btn btn-sm {{ $filter === $key ? 'btn-primary' : 'btn-outline-primary' }}">{{ $label }} <span class="badge bg-light text-dark ms-1">{{ $counts[$key] }}</span></a>
        @endforeach
    </div>

    <div class="card">
        <div class="card-body">
            @if ($reviews->isEmpty())
                <p class="text-center text-secondary my-4">No reviews here yet.</p>
            @else
            <div class="table-responsive">
                <table class="table align-middle hms-table" style="width:100%">
                    <thead>
                        <tr>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Guest &amp; stay</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td class="text-nowrap" style="color:#c9962c">{{ str_repeat('★', $review->rating) }}<span class="text-secondary">{{ str_repeat('☆', 5 - $review->rating) }}</span></td>
                            <td style="max-width:420px">
                                @if ($review->title)<strong>{{ $review->title }}</strong><br>@endif
                                <span class="text-wrap">{{ $review->comment }}</span>
                            </td>
                            <td>
                                {{ $review->user->name ?? $review->booking->name ?? 'Guest' }}<br>
                                <small class="text-secondary">
                                    {{ $review->room->type->name ?? 'Room' }}
                                    @if ($review->booking) · <a href="{{ route('edit_booking', $review->booking_id) }}">#{{ $review->booking->code }}</a> @endif
                                    <br>{{ $review->created_at->format('d M Y') }}
                                </small>
                            </td>
                            <td>
                                @php($badge = [0 => 'bg-warning-subtle text-warning', 1 => 'bg-success-subtle text-success', 2 => 'bg-secondary-subtle text-secondary'][(int) $review->status])
                                <span class="badge {{ $badge }}">{{ $review->statusLabel() }}</span>
                            </td>
                            <td class="text-end text-nowrap">
                                @if ((int) $review->status !== 1)
                                    <form method="POST" action="{{ route('review.status', $review->id) }}" class="d-inline">@csrf
                                        <input type="hidden" name="status" value="1">
                                        <button class="btn btn-sm btn-success">Publish</button>
                                    </form>
                                @endif
                                @if ((int) $review->status !== 2)
                                    <form method="POST" action="{{ route('review.status', $review->id) }}" class="d-inline">@csrf
                                        <input type="hidden" name="status" value="2">
                                        <button class="btn btn-sm btn-outline-secondary">Hide</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('review.delete', $review->id) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this review? The guest will be able to review that stay again.');">@csrf
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
