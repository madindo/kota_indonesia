<?php

namespace App\Console\Commands;

use App\Models\District;
use Illuminate\Console\Command;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use Carbon\Carbon;

class GeneratePlaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-places';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate to DB from CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Province::truncate();
        $file = public_path('csv/provinces.csv');
        $provinces = $this->csvToArray($file, ',', 'province');
        Province::insert($provinces);

        // Regency::truncate();
        // $file = public_path('csv/regencies.csv');
        // $regencies = $this->csvToArray($file, ',', 'regency');
        // Regency::insert($regencies);

        // District::truncate();
        // $file = public_path('csv/districts.csv');
        // $districts = $this->csvToArray($file, ',', 'district');
        // District::insert($districts);

        // Village::truncate();
        // $file = public_path('csv/villages.csv');
        // $villages = $this->csvToArray($file, ',', 'village');
        // foreach(array_chunk($villages, 5000) as $chunks ) {
        //     foreach ($chunks as $vil ) {
        //         Village::insert($vil);
        //     }
        // }
    }

    public function csvToArray($filename = '', $delimiter = ',' , $type = '')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $data = array();
        $now = Carbon::now()->toDateTimeString();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            $i = 0;
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if ($type == 'province') {
                    $data[$i]['province_id'] = $row[0];
                    $data[$i]['province_name'] = $row[1];
                    $data[$i]['created_at'] = $now;
                } else if ($type == 'regency') {
                    $data[$i]['regency_id'] = $row[0];
                    $data[$i]['province_id'] = $row[1];
                    $data[$i]['regency_name'] = $row[2];
                    $data[$i]['created_at'] = $now;
                } else if ($type == 'district') {
                    $data[$i]['district_id'] = $row[0];
                    $data[$i]['regency_id'] = $row[1];
                    $data[$i]['district_name'] = $row[2];
                    $data[$i]['created_at'] = $now;
                } else if ($type == 'village') {
                    if ( !empty($row[0]) || !empty($row[1]) || !empty($row[2]) ) {
                    $data[$i]['village_id'] = $row[0];
                    $data[$i]['district_id'] = $row[1];
                    $data[$i]['village_name'] = $row[2];
                    $data[$i]['created_at'] = $now;
                    }
                }
                $i++;
            }
            fclose($handle);
        }

        return $data;
    }
}
