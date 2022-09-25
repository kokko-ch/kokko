<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\NotificationJobResource;
use App\Models\NotificationJob;
use App\Models\User;
use Filament\Pages\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ViewException;
use Livewire;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Tests\TestCase;

class NotificationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(NotificationJobResource::getUrl('index'));

        $response->assertSuccessful();
    }

    public function test_page_can_list_notification_jobs(): void
    {
        $user = User::factory()->hasNotificationJobs(3)->create();

        $this->actingAs($user);

        $response = Livewire::test(
            NotificationJobResource\Pages\ListNotificationJobs::class
        );

        $response->assertCanSeeTableRecords($user->notificationJobs);
    }

    public function test_page_cannot_list_other_notification_jobs(): void
    {
        $user = User::factory()->hasNotificationJobs(3)->create();

        $this->actingAs($user);

        $response = Livewire::test(
            NotificationJobResource\Pages\ListNotificationJobs::class
        );

        $response->assertCanNotSeeTableRecords(
            User::factory()
                ->hasNotificationJobs(3)
                ->create()
                ->notificationJobs
        );
    }

    public function test_create_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->get(NotificationJobResource::getUrl('create'));

        $response->assertSuccessful();
    }

    public function test_create_page_can_create(): void
    {
        $user = User::factory()->create();
        $newData = NotificationJob::factory()->make();

        $this->actingAs($user);

        Livewire::test(
            NotificationJobResource\Pages\CreateNotificationJob::class
        )
            ->fillForm([
                'minute' => $newData->minute,
                'hour' => $newData->hour,
                'day' => $newData->day,
                'month' => $newData->month,
                'weekday' => $newData->weekday,
                'timezone' => $newData->timezone,
                'event' => $newData->event,
                'title' => $newData->title,
                'content' => $newData->content,
                'is_active' => $newData->is_active,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(NotificationJob::class, [
            'user_id' => $user->getKey(),
            'minute' => $newData->minute,
            'hour' => $newData->hour,
            'day' => $newData->day,
            'month' => $newData->month,
            'weekday' => $newData->weekday,
            'timezone' => $newData->timezone == $user->timezone
                          ? null
                          : $newData->timezone,
            'event' => $newData->event,
            'title' => empty($newData->title) ? null : $newData->title,
            'content' => $this->castAsJson($newData->content),
            'is_active' => intval($newData->is_active),
        ]);
    }

    public function test_create_page_can_validate_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(
            NotificationJobResource\Pages\CreateNotificationJob::class
        )
            ->fillForm([
                'minute' => null,
                'hour' => null,
                'day' => null,
                'month' => null,
                'weekday' => null,
                'event' => null,
                'content' => null,
                'is_active' => null,
            ])
            ->call('create')
            ->assertHasFormErrors([
                'minute' => 'required',
                'hour' => 'required',
                'day' => 'required',
                'month' => 'required',
                'weekday' => 'required',
                'event' => 'required',
                'content' => 'required',
                'is_active' => 'required',
            ]);
    }

    public function test_edit_page_can_be_rendered(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();

        $response = $this->actingAs($user)
                         ->get(NotificationJobResource::getUrl('edit', [
                             'record' => $user->notificationJobs()->first(),
                         ]));

        $response->assertSuccessful();
    }

    public function test_page_cannot_edit_other_notification_job(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();

        $response = $this->actingAs($user)
                         ->get(NotificationJobResource::getUrl('edit', [
                             'record' => User::factory()
                                             ->hasNotificationJobs()
                                             ->create()
                                             ->notificationJobs()
                                             ->first(),
                         ]));

        $response->assertNotFound();
    }

    public function test_edit_page_can_retrieve_data(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();
        $notificationJob = $user->notificationJobs()->first();

        $this->actingAs($user);

        $response = Livewire::test(
            NotificationJobResource\Pages\EditNotificationJob::class, [
                'record' => $notificationJob->getKey(),
            ]
        );

        $response->assertFormSet([
            'minute' => $notificationJob->minute,
            'hour' => $notificationJob->hour,
            'day' => $notificationJob->day,
            'month' => $notificationJob->month,
            'weekday' => $notificationJob->weekday,
            'timezone' => $notificationJob->timezone,
            'title' => $notificationJob->title,
            // 'content' => $notificationJob->content,
            'is_active' => $notificationJob->is_active,
        ]);

        $this->assertSame(
            array_values($response->get('data.content')),
            $notificationJob->content,
        );
    }

    public function test_edit_page_can_save(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();
        $notificationJob = $user->notificationJobs()->first();
        $newData = NotificationJob::factory()->make();

        $this->actingAs($user);

        Livewire::test(
            NotificationJobResource\Pages\EditNotificationJob::class, [
                'record' => $notificationJob->getKey(),
            ]
        )
            ->fillForm([
                'minute' => $newData->minute,
                'hour' => $newData->hour,
                'day' => $newData->day,
                'month' => $newData->month,
                'weekday' => $newData->weekday,
                'timezone' => $newData->timezone,
                'event' => $newData->event,
                'title' => $newData->title,
                'content' => $newData->content,
                'is_active' => $newData->is_active,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $notificationJob = $notificationJob->refresh();

        $this->assertSame($notificationJob->user_id, $user->getKey());
        $this->assertSame($notificationJob->minute, $newData->minute);
        $this->assertSame($notificationJob->hour, $newData->hour);
        $this->assertSame($notificationJob->day, $newData->day);
        $this->assertSame($notificationJob->month, $newData->month);
        $this->assertSame($notificationJob->weekday, $newData->weekday);
        $this->assertSame($notificationJob->timezone, $newData->timezone);
        $this->assertSame($notificationJob->event, $newData->event);
        $this->assertSame(
            $notificationJob->title,
            empty($newData->title) ? null : $newData->title
        );
        $this->assertSame($notificationJob->content, $newData->content);
        $this->assertSame($notificationJob->is_active, $newData->is_active);
    }

    public function test_edit_page_can_validate_input(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();

        $this->actingAs($user);

        Livewire::test(
            NotificationJobResource\Pages\EditNotificationJob::class, [
                'record' => $user->notificationJobs()->first()->getKey(),
            ]
        )
            ->fillForm([
                'minute' => null,
                'hour' => null,
                'day' => null,
                'month' => null,
                'weekday' => null,
                'event' => null,
                'content' => null,
                'is_active' => null,
            ])
            ->call('save')
            ->assertHasFormErrors([
                'minute' => 'required',
                'hour' => 'required',
                'day' => 'required',
                'month' => 'required',
                'weekday' => 'required',
                'event' => 'required',
                'content' => 'required',
                'is_active' => 'required',
            ]);
    }

    public function test_edit_page_can_delete(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();
        $notificationJob = $user->notificationJobs()->first();

        $this->actingAs($user);

        Livewire::test(
            NotificationJobResource\Pages\EditNotificationJob::class, [
                'record' => $notificationJob->getKey(),
            ]
        )
            ->callPageAction(DeleteAction::class);

        $this->assertModelMissing($notificationJob);
    }

    public function test_edit_page_cannot_delete_other_notification_job(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();

        $this->actingAs($user);

        $this->expectException(ViewException::class);

        Livewire::test(
            NotificationJobResource\Pages\EditNotificationJob::class, [
                'record' => User::factory()
                                ->hasNotificationJobs()
                                ->create()
                                ->notificationJobs()
                                ->first(),
            ]
        )
            ->callPageAction(DeleteAction::class);
    }

    public function test_view_page_cannot_be_rendered(): void
    {
        $user = User::factory()->hasNotificationJobs()->create();

        $this->expectException(RouteNotFoundException::class);

        $this->actingAs($user)->get(NotificationJobResource::getUrl('view', [
            'record' => $user->notificationJobs()->first()->getKey(),
        ]));
    }
}
