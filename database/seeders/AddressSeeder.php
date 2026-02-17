<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\District;
use App\Models\Province;
use App\Models\Village;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = [
            ['name' => 'Phnom Penh', 'code' => 'PP'],
            ['name' => 'Kandal', 'code' => 'KD'],
            ['name' => 'Siem Reap', 'code' => 'SR'],
            ['name' => 'Battambang', 'code' => 'BB'],
            ['name' => 'Prey Veng', 'code' => 'PV'],
        ];

        foreach ($provinces as $p) {
            $province = Province::firstOrCreate(['name' => $p['name']], ['code' => $p['code']]);

            $districts = [
                'Phnom Penh' => ['Chamkarmon', 'Daun Penh', 'Russey Keo', 'Tuol Kork'],
                'Kandal' => ['Kandal Stueng', 'Kien Svay', 'Ta Khmau'],
                'Siem Reap' => ['Siem Reap', 'Prasat Bakong', 'Banteay Srei'],
                'Battambang' => ['Battambang', 'Banan', 'Sangkae'],
                'Prey Veng' => ['Prey Veng', 'Neak Loeung', 'Peam Chor'],
            ][$province->name] ?? ['District 1'];

            foreach ($districts as $dName) {
                $district = District::firstOrCreate(
                    ['province_id' => $province->id, 'name' => $dName]
                );

                $communities = collect(range(1, 2))->map(fn ($i) => "{$dName} Comm {$i}")->all();
                foreach ($communities as $cName) {
                    $community = Community::firstOrCreate(
                        ['district_id' => $district->id, 'name' => $cName]
                    );

                    collect(range(1, 2))->each(function ($i) use ($community) {
                        Village::firstOrCreate(
                            ['community_id' => $community->id, 'name' => "Village {$i}"]
                        );
                    });
                }
            }
        }
    }
}
