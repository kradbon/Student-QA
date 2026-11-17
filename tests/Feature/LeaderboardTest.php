<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_leaderboard_ranks_active_students_by_reputation_and_breaks_ties_by_name(): void
    {
        User::factory()->create(['name' => 'A Leader', 'reputation' => 1500]);
        User::factory()->create(['name' => 'B Student', 'reputation' => 800]);
        $student = User::factory()->create(['name' => 'C Student', 'reputation' => 800]);
        User::factory()->create(['name' => 'Suspended User', 'status' => 'suspended', 'reputation' => 9000]);
        User::factory()->create(['name' => 'Moderator Account', 'role' => 'moderator', 'reputation' => 8000]);

        $this->actingAs($student)->get(route('community.leaderboard'))
            ->assertOk()
            ->assertSee('A Leader')
            ->assertSee('B Student')
            ->assertSee('C Student')
            ->assertSee('#3')
            ->assertDontSee('Suspended User')
            ->assertDontSee('Moderator Account');

        $this->get(route('community.leaderboard', ['q' => 'C Student']))
            ->assertOk()
            ->assertSee('id="student-row-'.$student->id.'"', false)
            ->assertSee('>3</span>', false);
    }

    public function test_personal_rank_and_shortcut_remain_available_beyond_first_page(): void
    {
        for ($rank = 1; $rank <= 21; $rank++) {
            User::factory()->create(['name' => 'Ranked Student '.$rank, 'reputation' => 1000 - $rank]);
        }

        $student = User::factory()->create(['name' => 'Student Outside First Page', 'reputation' => 0]);

        $this->actingAs($student)->get(route('community.leaderboard'))
            ->assertOk()
            ->assertSee('#22')
            ->assertSee('page=2#ranking-title', false);
    }
}
