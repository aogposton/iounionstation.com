<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Track;
use Illuminate\Http\Request;

class GetFreshContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;//, SerializesModels;

    public $tries = 5;
    
    public $thread;
    public $controller;
    public $function;
    public $sync;

    public function __construct(Thread $thread, string $controller, string $function, $sync = false)
    {
        $this->thread = $thread;
        $this->controller = $controller;
        $this->function = $function;
        $this->sync = $sync;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      app('\App\Http\Controllers\ThreadController')->getFreshContent($this->thread, $this->controller, $this->function, $this->sync);
    }
}
