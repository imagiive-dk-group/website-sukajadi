<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Packages\Pages\EditPackage;
use App\Models\Package;
use Filament\Forms\Components\Repeater;
use Livewire\Livewire;

class PackageFormTest extends AdminTestCase
{
    public function test_create_package_with_tier_persists_bilingual_json(): void
    {
        $this->actingAsSuperAdmin();

        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'name' => ['id' => 'Paket Uji Coba', 'en' => 'Test Package'],
                'summary' => ['id' => 'Ringkasan paket', 'en' => 'Package summary'],
                'duration' => ['id' => '4 jam', 'en' => '4 hours'],
                'description' => ['id' => '<p>Deskripsi <strong>lengkap</strong></p>', 'en' => '<p>Full description</p>'],
                'slug' => 'paket-uji-coba',
                'slug_en' => 'test-package',
                'category' => 'alam',
                'facilities' => ['guide', 'meals'],
                'target_groups' => ['school'],
                'itinerary' => [
                    ['time' => '08.00', 'activity' => ['id' => 'Kumpul', 'en' => 'Gather']],
                ],
                'tiers' => [
                    [
                        'name' => ['id' => 'Hemat', 'en' => 'Basic'],
                        'duration' => ['id' => '2 jam', 'en' => '2 hours'],
                        'badge' => ['id' => null, 'en' => null],
                        'price' => 65000,
                        'min_pax' => 15,
                        'is_dummy_price' => true,
                        'inclusions' => [
                            ['id' => 'Snack lokal', 'en' => 'Local snack'],
                        ],
                    ],
                ],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $package = Package::query()->where('slug', 'paket-uji-coba')->firstOrFail();

        $this->assertSame(['id' => 'Paket Uji Coba', 'en' => 'Test Package'], $package->name);
        $this->assertSame('Ringkasan paket', $package->summary['id']);
        $this->assertStringContainsString('<strong>lengkap</strong>', $package->description['id']);
        $this->assertSame('test-package', $package->slug_en);
        $this->assertSame(['guide', 'meals'], $package->facilities);
        $this->assertSame('Gather', $package->itinerary[0]['activity']['en']);

        $tier = $package->tiers()->firstOrFail();
        $this->assertSame(['id' => 'Hemat', 'en' => 'Basic'], $tier->name);
        $this->assertSame(65000, $tier->price);
        $this->assertTrue($tier->is_dummy_price);
        $this->assertSame([['id' => 'Snack lokal', 'en' => 'Local snack']], $tier->inclusions);

        // Data JSON mentah di database.
        $raw = json_decode($package->getRawOriginal('name'), true);
        $this->assertSame(['id' => 'Paket Uji Coba', 'en' => 'Test Package'], $raw);
    }

    public function test_slug_is_generated_from_indonesian_name_and_english_falls_back(): void
    {
        $this->actingAsSuperAdmin();

        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'name' => ['id' => 'Paket Kebun Teh'],
                'category' => 'alam',
                'tiers' => [
                    ['name' => ['id' => 'Standar'], 'price' => 100000, 'min_pax' => 10],
                ],
            ])
            ->assertSchemaStateSet(['slug' => 'paket-kebun-teh'])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $package = Package::query()->where('slug', 'paket-kebun-teh')->firstOrFail();
        $this->assertSame('paket-kebun-teh', $package->slug_en);
        $this->assertSame('Paket Kebun Teh', $package->tr('name', 'en'));
    }

    public function test_edit_package_updates_english_name(): void
    {
        $this->actingAsSuperAdmin();
        $this->seed(\Database\Seeders\TourismSeeder::class);

        $package = Package::query()->firstOrFail();

        Livewire::test(EditPackage::class, ['record' => $package->getRouteKey()])
            ->assertSchemaStateSet(['name.id' => $package->name['id']])
            ->fillForm(['name.en' => 'Updated English Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $package->refresh();
        $this->assertSame('Updated English Name', $package->name['en']);
        $this->assertNotEmpty($package->name['id']);
    }
}
