<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_dashboard_stats_returns_correct_data()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        // Create projects with different statuses
        Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'contacted',
            'amount' => 10000
        ]);
        
        Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'in_progress',
            'amount' => 20000
        ]);
        
        Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'completed',
            'amount' => 30000
        ]);
        
        Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'paid',
            'amount' => 40000
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_projects',
                    'total_clients',
                    'in_progress_projects',
                    'completed_projects',
                    'paid_projects',
                    'total_revenue',
                    'pending_revenue',
                    'potential_revenue',
                    'expected_revenue'
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(4, $data['total_projects']);
        $this->assertEquals(1, $data['total_clients']);
        $this->assertEquals(1, $data['in_progress_projects']);
        $this->assertEquals(1, $data['completed_projects']);
        $this->assertEquals(1, $data['paid_projects']);
        $this->assertEquals(40000, $data['total_revenue']); // Only paid projects
        $this->assertEquals(30000, $data['pending_revenue']); // Completed projects
        $this->assertEquals(20000, $data['potential_revenue']); // only in_progress projects
    }

    public function test_dashboard_activities_returns_recent_projects()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => 'Test Project',
            'status' => 'in_progress'
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/activities?limit=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'description',
                        'time',
                        'project_id',
                        'type'
                    ]
                ]
            ]);

        $activities = $response->json('data');
        $this->assertCount(1, $activities);
        $this->assertStringContainsString('Test Project', $activities[0]['description']);
    }

    public function test_revenue_trend_returns_monthly_data()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        Project::factory()->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'completed',
            'amount' => 50000,
            'payment_date' => now()->subMonth()
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/revenue/trend?months=6');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'month',
                        'month_name',
                        'revenue'
                    ]
                ]
            ]);

        $data = $response->json('data');
        $this->assertCount(6, $data);
    }

    public function test_project_status_distribution()
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['user_id' => $user->id]);
        
        // Create multiple projects with different statuses
        Project::factory()->count(2)->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'contacted',
            'amount' => 10000
        ]);
        
        Project::factory()->count(3)->create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'status' => 'in_progress',
            'amount' => 20000
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/dashboard/projects/status-distribution');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data'
            ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('contacted', $data);
        $this->assertArrayHasKey('in_progress', $data);
        $this->assertEquals(2, $data['contacted']['count']);
        $this->assertEquals(3, $data['in_progress']['count']);
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/api/dashboard/stats');

        $response->assertStatus(401);
    }
}