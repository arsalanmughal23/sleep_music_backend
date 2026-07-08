<?php

use Faker\Factory as Faker;
use App\Models\Media;
use App\Repositories\Admin\MediaRepository;

trait MakeMediaTrait
{
    /**
     * Create fake instance of Media and save it in database
     *
     * @param array $mediaFields
     * @return Media
     */
    public function makeMedia($mediaFields = [])
    {
        /** @var MediaRepository $mediaRepo */
        $mediaRepo = App::make(MediaRepository::class);
        $theme = $this->fakeMediaData($mediaFields);
        return $mediaRepo->create($theme);
    }

    /**
     * Get fake instance of Media
     *
     * @param array $mediaFields
     * @return Media
     */
    public function fakeMedia($mediaFields = [])
    {
        return new Media($this->fakeMediaData($mediaFields));
    }

    /**
     * Get fake data of Media
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMediaData($mediaFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->word,
            'category_id' => $fake->word,
            'name' => $fake->word,
            'is_featured' => $fake->word,
            'image' => $fake->word,
            'file_path' => $fake->word,
            'file_type' => $fake->word,
            'file_mime' => $fake->word,
            'file_url' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $mediaFields);
    }
}
