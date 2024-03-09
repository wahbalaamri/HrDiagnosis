<?php

namespace App\Jobs;

use App\Mail\SendDemoCPLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDemoLinkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $details;
	protected $user;
    public $timeout = 7200; // 2 hours

    public function __construct($details,$user)
    {
        //
        $this->details = $details;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $data=$this->details;
        Mail::to($this->user)->send(new SendDemoCPLink($data));
    }
}
