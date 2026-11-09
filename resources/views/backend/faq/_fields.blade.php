@php($faq = $faq ?? null)

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row mb-3">
    <div class="col-sm-3"><label class="form-label mb-0" for="f-question">Question</label></div>
    <div class="col-sm-9">
        <input id="f-question" type="text" name="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}" required>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-3"><label class="form-label mb-0" for="f-answer">Answer</label></div>
    <div class="col-sm-9">
        <textarea id="f-answer" name="answer" rows="4" class="form-control" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
    </div>
</div>
<div class="row mb-3">
    <div class="col-sm-3"><label class="form-label mb-0" for="f-sort_order">Order</label></div>
    <div class="col-sm-9">
        <input id="f-sort_order" type="number" name="sort_order" min="0" max="999" class="form-control" value="{{ old('sort_order', $faq->sort_order ?? 0) }}">
        <small class="text-muted">Smaller numbers appear first.</small>
    </div>
</div>
