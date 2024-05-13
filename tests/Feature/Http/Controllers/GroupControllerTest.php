<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Group;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Group::factory(10)->create();
    }

    public function test_get_list_of_all_groups(): void
    {
        $response = $this->get('/groups');
        $response->assertOk();
        $response->assertViewIs('groups.index');
        $response->assertViewHas('groups', Group::all());
    }

    public function test_get_group_information()
    {
        $group = Group::all()->shuffle()->first();

        $response = $this->get("/groups/{$group->id}");
        $response->assertViewIs('groups.show');
        $response->assertViewHas('group', $group);
    }

    public function test_redirect_to_groups_index_on_invalid_group()
    {
        $invalidGroupId = Group::all()->last()->id + 1;
        $response = $this->get("/groups/{$invalidGroupId}");
        $response->assertRedirectToRoute('group.index', [
            'error' => 'Requested resource does not exist.'
        ]);
    }

    public function test_create_group_get_method_returns_view()
    {
        $response = $this->get('/groups/create');
        $response->assertOk();
        $response->assertViewIs('groups.create');
    }




    public function test_create_group_reject_invalid_values()
    {

        $response = $this->post('/groups/create', []);
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);
        $groupPayload = [
            'title' => fake()->regexify('/{A-Za-z0-9}{300}/'),
        ];

        $response = $this->post('/groups/create', $groupPayload);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);

        $this->assertDatabaseMissing('tasks', $groupPayload);
    }

    public function test_create_group_should_accept_valid_values()
    {
        $title = fake()->text(64);

        $response = $this->post('/groups/create', [
            'title' => $title
        ]);
        $response->assertRedirectToRoute('group.index', [
            'message' => 'New group has been created.'
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title
        ]);
    }

    public function test_delete_group_get_method_returns_view()
    {
        $group = Group::all()->shuffle(1)->first();

        $response = $this->get("/groups/{$group->id}/delete");
        $response->assertOk();
        $response->assertViewIs('groups.delete');
        $response->assertViewHas('group', $group);
    }

    public function test_delete_group_should_return_to_groups_index()
    {
        $group = Group::all()->shuffle()->first();

        $response = $this->delete("/groups/{$group->id}/delete");
        $response->assertRedirectToRoute('group.index', [
            'message' => 'Group is deleted.'
        ]);
    }


    public function test_edit_group_get_method_returns_view()
    {
        $group = Group::all()->shuffle(1)->first();

        $response = $this->get("/groups/{$group->id}/edit");
        $response->assertOk();
        $response->assertViewIs('groups.edit');
        $response->assertViewHas('group', $group);
    }

    public function test_edit_group_should_reject_on_invalid_payload()
    {
        $group = Group::all()->shuffle()->first();

        $response = $this->put("/groups/{$group->id}/edit");
        $response->assertSessionHasErrors([
            'title' => 'The title field is required.',
        ]);

        $response = $this->put("/groups/{$group->id}/edit", [
            'title' => fake()->regexify('/[A-Za-z0-9]{200}/')
        ]);
        $response->assertSessionHasErrors([
            'title' => 'The title field must not be greater than 64 characters.',
        ]);
    }

    public function test_edit_group_should_accept_valid_payload()
    {
        $group = Group::all()->shuffle()->first();
        $title = fake()->text(64);

        $response = $this->put("/groups/{$group->id}/edit", [
            'title' => $title
        ]);
        $response->assertRedirectToRoute('group.show', [
            'group' => $group
        ]);
        $this->assertDatabaseMissing('groups', [
            'title' => $group->title
        ]);
        $this->assertDatabaseHas('groups', [
            'title' => $title,
        ]);
    }
}
