<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\Contact;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class GalleryController extends Controller
{
    public function AllGallery(){

        $gallery = Gallery::latest()->get();
        return view('backend.gallery.all_gallery',compact('gallery'));

    } // End Method 

    public function AddGallery(){
        return view('backend.gallery.add_gallery');
    } // End Method 

    public function StoreGallery(Request $request){

        $request->validate([
            'photo_name' => 'required|array',
            'photo_name.*' => 'image|max:5120',
            'caption' => 'nullable|string|max:180',
        ]);

        $images = $request->file('photo_name');
        $show = $request->boolean('show_on_home');

        foreach ($images as $img) {
            $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            @mkdir(public_path('upload/gallery'), 0755, true);
            Image::make($img)->resize(1400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(public_path('upload/gallery/'.$name_gen), 88);

            Gallery::create([
                'photo_name' => 'upload/gallery/'.$name_gen,
                'caption' => $request->input('caption'),
                'show_on_home' => $show,
            ]);
        }

        if ($show) {
            $this->limitHomepagePhotos();
        }

        $notification = array(
            'message' => 'Gallery Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.gallery')->with($notification);

    }// End Method 


    public function EditGallery($id){

        $gallery = Gallery::find($id);
        return view('backend.gallery.edit_gallery',compact('gallery'));

    }// End Method 

    public function UpdateGallery(Request $request){

        $request->validate([
            'id' => 'required|integer',
            'photo_name' => 'nullable|image|max:5120',
            'caption' => 'nullable|string|max:180',
        ]);

        $gallery = Gallery::findOrFail($request->id);
        $data = [
            'caption' => $request->input('caption'),
            'show_on_home' => $request->boolean('show_on_home'),
        ];

        if ($request->file('photo_name')) {
            $img = $request->file('photo_name');
            $name_gen = hexdec(uniqid()).'.'.$img->getClientOriginalExtension();
            @mkdir(public_path('upload/gallery'), 0755, true);
            Image::make($img)->resize(1400, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->save(public_path('upload/gallery/'.$name_gen), 88);
            $data['photo_name'] = 'upload/gallery/'.$name_gen;
        }

        $gallery->update($data);

        if ($gallery->show_on_home) {
            $this->limitHomepagePhotos();
        }

        $notification = array(
            'message' => 'Gallery Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.gallery')->with($notification);

    }// End Method

    private function deleteGalleryFile(?string $path): void
    {
        foreach (array_filter([$path, $path ? public_path($path) : null]) as $file) {
            if (is_file($file)) {
                unlink($file);
                return;
            }
        }
    }

    /** The homepage photo strip keeps the three newest flagged pictures. */
    private function limitHomepagePhotos(): void
    {
        // Slice in PHP: SQLite rejects OFFSET without LIMIT.
        $drop = Gallery::where('show_on_home', true)->orderByDesc('id')->pluck('id')->slice(3)->values();
        if ($drop->isNotEmpty()) {
            Gallery::whereIn('id', $drop)->update(['show_on_home' => false]);
        }
    } 

    public function DeleteGallery($id){

        $item = Gallery::findOrFail($id);
        $this->deleteGalleryFile($item->photo_name);

        Gallery::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Gallery Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);


     }   // End Method 


     public function DeleteGalleryMultiple(Request $request){

        $selectedItems = $request->input('selectedItem', []);

        foreach ($selectedItems as $itemId) {
           $item = Gallery::find($itemId);
           if ($item) {
               $this->deleteGalleryFile($item->photo_name);
               $item->delete();
           }
        }

        $notification = array(
            'message' => 'Selected Image Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);

     }// End Method 


     public function ShowGallery(){
        $gallery = Gallery::latest()->get();
        return view('frontend.gallery.show_gallery',compact('gallery'));
     }// End Method


     public function ContactUs(){

        return view('frontend.contact.contact_us', [
            'reasons' => $this->contactReasons(),
        ]);
     }// End Method


     public function StoreContactUs(Request $request){

        $reasons = $this->contactReasons();

        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'nullable|string|max:30',
            'reason'  => 'required|in:'.implode(',', array_keys($reasons)),
            'subject' => 'required|string|max:120',
            'message' => 'required|string|max:3000',
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => (string) $request->phone, // column is NOT NULL; phone is optional on the form
            'subject' => $reasons[$request->reason].' — '.$request->subject,
            'message' => $request->message,
        ]);

        $notification = array(
            'message' => 'Thank you! Your message has been sent. We will get back to you shortly.',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

     }// End Method


     /** Reasons a guest can choose. Stored as the start of the subject. */
     private function contactReasons(): array
     {
        return [
            'reservation' => 'A new reservation',
            'booking' => 'A booking I already have',
            'event' => 'An event or meeting',
            'other' => 'Something else',
        ];
     }


     public function AdminContactMessage(){

        $contact = Contact::latest()->get();
        return view('backend.contact.contact_message',compact('contact'));

     }// End Method



}
