<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function AllFaq()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->get();

        return view('backend.faq.all_faq', compact('faqs'));
    }

    public function AddFaq()
    {
        return view('backend.faq.add_faq');
    }

    public function StoreFaq(Request $request)
    {
        Faq::create($this->validated($request));

        return redirect()->route('all.faq')->with([
            'message' => 'Question added',
            'alert-type' => 'success',
        ]);
    }

    public function EditFaq($id)
    {
        $faq = Faq::findOrFail($id);

        return view('backend.faq.edit_faq', compact('faq'));
    }

    public function UpdateFaq(Request $request)
    {
        Faq::findOrFail($request->id)->update($this->validated($request));

        return redirect()->route('all.faq')->with([
            'message' => 'Question updated',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteFaq($id)
    {
        Faq::findOrFail($id)->delete();

        return redirect()->back()->with([
            'message' => 'Question deleted',
            'alert-type' => 'success',
        ]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string|max:2000',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
