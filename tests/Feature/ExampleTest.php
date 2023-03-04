<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Track;
use App\Models\TrackType;
use App\Http\Controllers\TrackController;

class ExampleTest extends TestCase
{
  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_the_application_returns_a_successful_response()
  {
    $tracks = Track::all();
    foreach ($tracks as $track) {
      if ($track->track_type_id == TrackType::where('name', 'Cargo')->first()->id) {
        app(TrackController::class)->processCargoTrack($track, true);
      }
    }
  }
}
