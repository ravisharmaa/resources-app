<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Rules\ValidUrl;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ResourcesController extends Controller
{
    public function create(): Response
    {
        $validator = \validator(request()->all(), [
            'type' => 'required',
            'properties.title' => 'required',
            'properties.link' => ['required_if:type,link', new ValidUrl()],
            'properties.file' => ['required_if:type,pdf', 'mimetypes:application/pdf'],
            'properties.description' => ['required_if:type,html'],
            'properties.snippet' => ['required_if:type,html'],
        ], [
            'properties.title.required' => 'The title is required.',
            'properties.description.required_if' => 'The description is required.',
            'properties.file.required_if' => 'The file is required and must be a pdf.',
            'properties.file.mimetypes' => 'The file is required and must be a pdf.',
            'properties.snippet.required_if' => 'The snippet is required.',
            'properties.link.required_if' => 'The link is required.',
        ]);

        if ($validator->fails()) {
            return \response($validator->errors()->all(), 422);
        }

        $record = match (request('type')) {
            'pdf' => $this->uploadPdf(),
            'html', 'link' => request('properties')
        };

        Resource::create([
            'name' => request('type'),
            'properties' => $record,
        ]);

        return \response(Resource::all(), 201);
    }

    public function update(Resource $resource): Response
    {
        $validator = \validator(request()->all(), [
            'type' => ['required', function ($attribute, $value, $fail) use ($resource) {
                if ($resource->name != $value) {
                    $fail('The resource names mismatch.');
                }
            }],
            'properties.title' => 'required',
            'properties.link' => ['required_if:type,link', new ValidUrl()],
            'properties.file' => ['nullable', 'mimetypes:application/pdf'],
            'properties.description' => ['required_if:type,html'],
            'properties.snippet' => ['required_if:type,html'],
        ], [
            'properties.title.required' => 'The title is required.',
            'properties.description.required_if' => 'The description is required.',
            'properties.file.mimetypes' => 'The file must be a pdf file, if you wish to update it.',
            'properties.snippet.required_if' => 'The snippet is required.',
            'properties.link.required_if' => 'The link is required.',
        ]);

        if ($validator->fails()) {
            return \response($validator->errors()->all(), 422);
        }

        $pdfData = [];

        if (request()->hasFile('properties.file')) {
            $this->deletePdfResourceFile($resource);
            $pdfData = $this->uploadPdf();
        }

        $record = match (request('type')) {
            'pdf' => count($pdfData) ? $pdfData : ['title' => request('properties.title'), 'path' => $resource->properties['path']],
            'html', 'link' => request('properties')
        };

        $resource->update([
           'properties' => $record,
        ]);

        return \response([], 200);
    }

    public function delete(Resource $resource): Response
    {
        $this->deletePdfResourceFile($resource);
        $resource->delete();
        return response(Resource::all(), 200);
    }

    private function uploadPdf(): array
    {
        $fileName = time() . '_resource.pdf';
        request()->file('properties.file')
            ->storeAs('pdf_resources', $fileName, 'public');
        return ['title' => request('properties.title'), 'path' => $fileName];
    }

    private function deletePdfResourceFile(Resource $resource): void
    {
        if ('pdf' == $resource->name) {
            Storage::disk('public')->delete('pdf_resources/' . $resource->properties['path']);
        }
    }
}
