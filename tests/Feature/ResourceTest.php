<?php

namespace Tests\Feature;

use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResourceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */

    public function admin_can_view_all_resources()
    {
        $this->get('/admin')->assertViewIs('resource');
    }

    /**
     * @test
     */
    public function admin_must_add_title_to_add_pdf_resource()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('pdf_resources/document.pdf', 500, 'application/pdf');
        $this->postJson(route('resources.create'), [
            'type' => 'pdf',
            'properties' => [
                'file' => $file,
            ],
        ])->assertUnprocessable()
            ->assertSee('The title is required');
    }

    /**
     * @test
     */
    public function admin_must_add_file_to_pdf_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'pdf',
            'properties' => [
                'title' => $this->faker->text,
            ],
        ])->assertUnprocessable()
            ->assertSee('The file is required and must be a pdf.');
    }

    /**
     * @test
     */
    public function admin_must_provide_a_pdf_file_to_create_pdf_resources()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('pdf_resources/document.jpg', 500, 'image/png');

        $this->postJson(route('resources.create'), [
            'type' => 'pdf',
            'properties' => [
                'title' => 'test-file',
                'file' => $file,
            ],
        ])->assertUnprocessable()
            ->assertSee('The file is required and must be a pdf.');
    }

    /**
     * @test
     */
    public function admin_can_create_a_pdf_resource_with_a_file()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('pdf_resources/document.pdf', 500, 'application/pdf');

        $this->postJson(route('resources.create'), [
            'type' => 'pdf',
            'properties' => [
                'title' => 'test-file',
                'file' => $file,
            ],
        ])->assertCreated();

        $resource = Resource::first();

        Storage::disk('public')->assertExists('pdf_resources/'.$resource->properties['path']);
        Storage::disk('public')->delete('pdf_resources/'.$resource->properties['path']);
        $this->assertDatabaseCount('resources', 1);
    }

    /**
     * @test
     */
    public function admin_can_add_a_html_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'html',
            'properties' => [
                'title' => 'html-snippet',
                'description' => $this->faker->text,
                'snippet' => '<p class="test"> a html snippet </p>',
            ],
        ])->assertCreated();

        $this->assertDatabaseCount('resources', 1);
    }

    /**
     * @test
     */
    public function admin_must_provide_description_for_html_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'html',
            'properties' => [
                'title' => 'html-snippet',
                'snippet' => '<p class="test"> a html snippet </p>',
            ],
        ])->assertUnprocessable()->assertSee(['The description is required.']);
    }

    /**
     * @test
     */
    public function admin_must_provide_snippet_for_html_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'html',
            'properties' => [
                'title' => $this->faker->title,
                'description' => $this->faker->text,
            ],
        ])->assertUnprocessable()->assertSee(['The snippet is required.']);
    }

    /**
     * @test
     */
    public function admin_can_add_a_link_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'link',
            'properties' => [
                'title' => 'some_link',
                'link' => 'https://google.com',
            ],
        ])->assertCreated();

        $this->assertDatabaseCount('resources', 1);
    }

    /**
     * @test
     */
    public function admin_must_provide_link_to_add_link_resource()
    {
        $this->postJson(route('resources.create'), [
            'type' => 'link',
            'properties' => [
                'title' => 'some_link',
            ],
        ])->assertUnprocessable()->assertSee('The link is required.');
    }

    /**
     * @test
     */
    public function admin_can_delete_a_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $this->deleteJson(route('resources.delete', ['resource' => $resource]))
            ->assertOk();
        $this->assertModelMissing($resource);
    }

    /**
     * @test
     */
    public function it_deletes_the_pdf_when_deleting_the_pdf_resource()
    {
        Storage::fake('public')->put('pdf_resources/dummy.pdf', 'dummy-content');

        $resource = Resource::create([
            'name' => 'pdf',
            'properties' => [
                'title' => 'A dummy pdf file',
                'path' => 'dummy.pdf',
            ],
        ]);

        $this->deleteJson(route('resources.delete', ['resource' => $resource]))->assertOk();

        Storage::disk('public')->assertMissing('pdf_resources/dummy.pdf');
    }

    /**
     * @test
     */
    public function admin_must_provide_title_to_update_a_pdf_resource()
    {
        $resource = Resource::create([
            'name' => 'pdf',
            'properties' => [
                'title' => 'A dummy pdf file',
                'path' => 'dummy.pdf',
            ],
        ]);

        $this->putJson(route('resources.update', ['resource' => $resource]))
            ->assertUnprocessable();
    }

    /**
     * @test
     */
    public function admin_need_not_provide_pdf_while_updating_pdf_resource()
    {
        Storage::fake('public')->put('pdf_resources/dummy.pdf', 'dummy-content');
        $resource = Resource::create([
            'name' => 'pdf',
            'properties' => [
                'title' => 'A dummy pdf file',
                'path' => 'dummy.pdf',
            ],
        ]);

        $this->putJson(route('resources.update', ['resource' => $resource], ), [
            'type' => 'pdf',
            'properties' => [
                'title' => 'I want to update the pdf file',
            ],
        ])->assertOk();

        $this->assertSame(Resource::first()->properties['title'], 'I want to update the pdf file');
        Storage::disk('public')->assertExists('pdf_resources/dummy.pdf');
        Storage::disk('public')->delete('pdf_resources/dummy.pdf');
    }

    /**
     * @test
     */
    public function it_replaces_the_pdf_file_while_updating_the_pdf_resource()
    {
        Storage::fake('public')->put('pdf_resources/dummy.pdf', 'dummy-content');
        $resource = Resource::create([
            'name' => 'pdf',
            'properties' => [
                'title' => 'A dummy pdf file',
                'path' => 'dummy.pdf',
            ],
        ]);

        $file = UploadedFile::fake()->create('pdf_resources/document.pdf', 500, 'application/pdf');

        $this->putJson(route('resources.update', ['resource' => $resource]), [
            'type' => 'pdf',
            'properties' => [
                'title' => 'I want to update the pdf file',
                'file' => $file,
            ],
        ])->assertOk();

        Storage::disk('public')->assertMissing('pdf_resources/dummy.pdf');
        Storage::disk('public')->assertExists('pdf_resources/'.$resource->fresh()->properties['path']);
        Storage::disk('public')->delete('pdf_resources/'.$resource->fresh()->properties['path']);
    }

    /**
     * @test
     */
    public function admin_can_update_html_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $updateData = [
            'type' => 'html',
            'properties' => [
                'title' => 'I want to update the pdf file',
                'description' => 'Updated description',
                'snippet' => 'Updated snippet',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)->assertOk();
        $properties = $resource->fresh()->properties;

        $this->assertSame($properties['title'], $updateData['properties']['title']);
        $this->assertSame($properties['description'], $updateData['properties']['description']);
        $this->assertSame($properties['snippet'], $updateData['properties']['snippet']);
    }

    /**
     * @test
     */
    public function admin_must_provide_title_to_update_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $updateData = [
            'type' => 'html',
            'properties' => [
                'description' => 'Updated description',
                'snippet' => 'Updated snippet',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The title is required');
    }

    /**
     * @test
     */
    public function admin_must_provide_description_to_update_html_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $updateData = [
            'type' => 'html',
            'properties' => [
                'title' => 'A sample title',
                'snippet' => 'Updated snippet',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The description is required');
    }

    /**
     * @test
     */
    public function admin_must_provide_snippet_to_update_html_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $updateData = [
            'type' => 'html',
            'properties' => [
                'title' => 'A sample title',
                'description' => 'A description update',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The snippet is required');
    }

    /**
     * @test
     */
    public function admin_can_update_link_resource()
    {
        $resource = Resource::factory()->linkResource()->create();

        $updateData = [
            'type' => 'link',
            'properties' => [
                'title' => 'I want to update the pdf file',
                'link' => 'https://google.com',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)->assertOk();
        $properties = $resource->fresh()->properties;

        $this->assertSame($properties['title'], $updateData['properties']['title']);
        $this->assertSame($properties['link'], $updateData['properties']['link']);
    }

    /**
     * @test
     */
    public function admin_must_provide_link_to_update_link_resource()
    {
        $resource = Resource::factory()->linkResource()->create();
        $updateData = [
            'type' => 'link',
            'properties' => [
                'title' => 'A sample title',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The link is required');
    }

    /**
     * @test
     */
    public function admin_can_not_update_link_to_a_html_resource()
    {
        $resource = Resource::factory()->linkResource()->create();
        $updateData = [
            'type' => 'html',
            'properties' => [
                'title' => 'A sample title',
                'description' => 'A description',
                'snippet' => 'A snippet'
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The resource names mismatch');
    }

    /**
     * @test
     */
    public function admin_can_not_update_html_to_a_link_resource()
    {
        $resource = Resource::factory()->htmlResource()->create();
        $updateData = [
            'type' => 'link',
            'properties' => [
                'title' => 'A sample title',
                'link' => 'https://google.com',
            ],
        ];

        $this->putJson(route('resources.update', ['resource' => $resource]), $updateData)
            ->assertUnprocessable()
            ->assertSee('The resource names mismatch');
    }
}
