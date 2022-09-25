<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Pages\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ViewException;
use Livewire;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(UserResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_page_can_list_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test(UserResource\Pages\ListUsers::class);

        $response->assertCanSeeTableRecords([$user]);
    }

    public function test_page_cannot_list_other_users(): void
    {
        $users = User::factory()->count(10)->create();

        $this->actingAs($users->pop());

        $response = Livewire::test(UserResource\Pages\ListUsers::class);

        $response->assertCanNotSeeTableRecords($users);
    }

    public function test_create_page_cannot_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(UserResource::getUrl('create'));

        $response->assertForbidden();
    }

    public function test_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(UserResource::getUrl('edit', [
            'record' => $user,
        ]));

        $response->assertSuccessful();
    }

    public function test_page_cannot_edit_other_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(UserResource::getUrl('edit', [
            'record' => User::factory()->create(),
        ]));

        $response->assertNotFound();
    }

    public function test_edit_page_can_retrieve_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $user->getKey(),
        ]);

        $response->assertFormSet([
            'name' => $user->name,
            'email' => $user->email,
            'timezone' => $user->timezone,
            'ifttt_key' => $user->ifttt_key,
        ]);
    }

    public function test_edit_page_can_save(): void
    {
        $user = User::factory()->create();
        $newData = User::factory()->make();

        $this->actingAs($user);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $user->getKey(),
        ])
            ->fillForm([
                'name' => $newData->name,
                'email' => $newData->email,
                'timezone' => $newData->timezone,
                'ifttt_key' => $newData->ifttt_key,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $user = $user->refresh();

        $this->assertSame($user->name, $newData->name);
        $this->assertSame($user->email, $newData->email);
        $this->assertSame($user->timezone, $newData->timezone);
        $this->assertSame($user->ifttt_key, $newData->ifttt_key);
    }

    public function test_edit_page_can_validate_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $user->getKey(),
        ])
            ->fillForm([
                'name' => null,
                'email' => null,
                'password' => null,
                'timezone' => null,
                'ifttt_key' => null,
            ])
            ->call('save')
            ->assertHasFormErrors([
                'name' => 'required',
                'email' => 'required',
            ]);
    }

    public function test_edit_page_can_delete(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => $user->getKey(),
        ])
            ->callPageAction(DeleteAction::class);

        $this->assertModelMissing($user);
    }

    public function test_edit_page_cannot_delete_other_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->expectException(ViewException::class);

        Livewire::test(UserResource\Pages\EditUser::class, [
            'record' => User::factory()->create()->getKey(),
        ])
            ->callPageAction(DeleteAction::class);
    }

    public function test_view_page_cannot_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->expectException(RouteNotFoundException::class);

        $this->actingAs($user)->get(UserResource::getUrl('view', [
            'record' => $user,
        ]));
    }
}
