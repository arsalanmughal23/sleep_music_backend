<?php

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryAndSoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement('TRUNCATE TABLE `categories`');
        DB::statement('TRUNCATE TABLE `media`');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $allData = config('category_and_sounds');
        $medias = [];

        forEach($allData as $data){ 
            $category = Category::create([
                'is_premium' => $data['is_premium'] ? 1 : 0,
                'is_mixer' => $data['is_mixer'] ? 1 : 0,
                'is_unlockable' => $data['is_unlockable'] ? 1 : 0,
                'name' => $data['name'],
                'image' => $data['image'],
                'type' => $data['type']
            ]);
            
            if(isset($data['sounds']) && is_array($data['sounds'])){
                forEach($data['sounds'] as $sound){

                    array_push($medias, [
                        'category_id' => $category->id,
                        'is_premium' => $sound['is_premium'] ? 1 : 0,
                        'name' => $sound['name'],
                        'is_mixer' => $sound['is_mixer'] ? 1 : 0,
                        'is_unlockable' => $sound['is_unlockable'] ? 1 : 0,
                        'user_id' => $sound['user_id'],
                        'image' => $sound['image'],
                        'file_type' => $sound['file_type'],
                        'file_path' => $sound['file_path'],
                        'file_mime' => $sound['file_mime'],
                        'file_url' => $sound['file_url'],
                    ]);
                }
            }
        }
        DB::table('media')->insert($medias);
    }
}
