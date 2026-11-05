<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    private const UPLOAD_DIR = 'upload/service';

    public function AllService()
    {
        $services = Service::latest()->get();

        return view('backend.service.all_service', compact('services'));
    }

    public function AddService()
    {
        return view('backend.service.add_service');
    }

    public function StoreService(Request $request)
    {
        $data = $this->validated($request);
        $data['image'] = $this->storeImage($request);

        Service::create($data);

        return redirect()->route('all.service')->with([
            'message' => 'Service added successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function EditService($id)
    {
        $service = Service::findOrFail($id);

        return view('backend.service.edit_service', compact('service'));
    }

    public function UpdateService(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($service->image);
            $data['image'] = $this->storeImage($request);
        }

        $service->update($data);

        return redirect()->route('all.service')->with([
            'message' => 'Service updated successfully.',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteService($id)
    {
        $service = Service::findOrFail($id);
        $this->deleteImage($service->image);
        $service->delete();

        return back()->with([
            'message' => 'Service deleted.',
            'alert-type' => 'success',
        ]);
    }

    // Public service page
    public function ShowService($id)
    {
        $service = Service::findOrFail($id);
        $otherServices = Service::where('id', '!=', $service->id)->latest()->limit(4)->get();

        return view('frontend.service.details', compact('service', 'otherServices'));
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'icon_class'  => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        return $request->only('title', 'description', 'icon_class');
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $name = uniqid('service_') . '.' . $file->getClientOriginalExtension();
        $file->move(public_path(self::UPLOAD_DIR), $name);

        return self::UPLOAD_DIR . '/' . $name;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
