<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_get_projects_list()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'category',
                            'amount',
                            'status',
                            'client',
                            'user'
                        ]
                    ]
                ]
            ]);
    }

    public function test_admin_can_see_all_projects()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        
        $client1 = Client::factory()->create(['user_id' => $admin->id]);
        $client2 = Client::factory()->create(['user_id' => $user->id]);
        
        $adminProject = Project::factory()->create([
            'user_id' => $admin->id,
            'client_id' => $client1->id
        ]);
        
        $userProject = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client2->id
        ]);

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    public function test_regular_user_can_only_see_own_projects()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $client1 = Client::factory()->create(['user_id' => $user1->id]);
        $client2 = Client::factory()->create(['user_id' => $user2->id]);
        
        $project1 = Project::factory()->create([
            'user_id' => $user1->id,
            'client_id' => $client1->id
        ]);
        
        $project2 = Project::factory()->create([
            'user_id' => $user2->id,
            'client_id' => $client2->id
        ]);

        Sanctum::actingAs($user1);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals($project1->id, $data[0]['id']);
    }

    public function test_can_create_project()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user);

        $projectData = [
            'name' => 'Test Project',
            'client_id' => $client->id,
            'description' => 'Test project description',
            'category' => 'website',
            'amount' => 50000,
            'contact_date' => '2025-01-01',
            'status' => 'contacted'
        ];

        $response = $this->postJson('/api/projects', $projectData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'category',
                    'amount',
                    'status'
                ]
            ]);

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);
    }

    public function test_can_update_project()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);

        Sanctum::actingAs($user);

        $updateData = [
            'name' => 'Updated Project Name',
            'status' => 'in_progress',
            'amount' => 50000,
            'expected_completion_date' => '2025-03-01'
        ];

        $response = $this->putJson("/api/projects/{$project->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Updated Project Name',
            'status' => 'in_progress'
        ]);
    }

    public function test_can_delete_project()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/projects/{$project->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id
        ]);
    }

    public function test_project_validation_rules()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/projects', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'client_id', 'category', 'contact_date', 'status']);
    }

    public function test_project_search_functionality()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        $project1 = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Website Development'
        ]);
        
        $project2 = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Mobile App'
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/projects?search=Website');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals('Website Development', $data[0]['name']);
    }

    public function test_project_status_filter()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        $project1 = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'contacted'
        ]);
        
        $project2 = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'in_progress'
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/projects?status=contacted');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertEquals('contacted', $data[0]['status']);
    }

    public function test_unauthenticated_user_cannot_access_projects()
    {
        $response = $this->getJson('/api/projects');

        $response->assertStatus(401);
    }
}