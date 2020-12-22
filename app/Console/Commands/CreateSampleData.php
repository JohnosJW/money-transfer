<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Console\Command;

/**
 * Class CreateSampleData
 * @package App\Console\Commands
 */
class CreateSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sample-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command created a sample data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /** @command */
    public function handle(): void
    {
        foreach ([1, 2, 3] as $item) {
            /** @var  $validatedData */
            $userData = [
                'name' => 'User' . $item,
                'email' => 'user' . $item . '@app.app',
            ];

            /** @var  $user */
            $user = new User($userData);
            $user->password = bcrypt('123456');
            $user->save();

            /** @var  $walletOfFirstUser */
            $wallet = Wallet::create([
                'user_id' => $user->id,
            ]);

            print "User: " . $user->email . " Wallet: " . $wallet->address;
            print "\n";
        }
    }
}
