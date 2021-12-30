<?php

namespace App\Console\Commands;

use App\Models\Quotation;
use Illuminate\Console\Command;

class Quotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Radnomly Selects a quotation from a database and update it.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {



        $Quote = Quotation::where('show', False)->inRandomOrder()->first();

        // Set Today's Quotation to True
        Quotation::where('show', True)->update(['show' => False]);
        $Quote->update(['show' => True]);



        print($Quote->quote);
        return Command::SUCCESS;
    }
}
