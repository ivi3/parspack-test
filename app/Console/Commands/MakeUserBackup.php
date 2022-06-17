<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Console\Command;

class MakeUserBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:backup {username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Zip files by username';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $user = User::where('username',$this->argument('username'))->first();
        if ($user){
            (new UserController())->usersBackupData();
        }else{
            $this->error("couldn't find any user by selected username");
        }
    }
}
