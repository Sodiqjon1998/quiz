<?php

namespace App\Console\Command;

use File;
use Illuminate\Console\Command;

class ClearTempImages extends Command
{
    protected $signature = 'cleanup:temp-images';
    protected $description = 'Remove images from uploads/tmp older than 1 day';

    public function handle()
    {
        while (true) { // Takroran ishga tushish uchun while loop
            $files = File::files(public_path('uploads/tmp'));
            $now = now();

            foreach ($files as $file) {
                // Faylning oxirgi o'zgartirilgan vaqtini tekshirib, 5 soniyadan eski bo'lsa o'chirish
                if ($now->diffInSeconds($file->getMTime()) > 5) {
                    // Faqat rasm fayllarini o'chirish
                    if (in_array($file->getExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
                        File::delete($file);
                        $this->info('Deleted: ' . $file->getFilename());
                    }
                }
            }

            // Har 5 soniyada bir marta tekshiradi
            sleep(5);
        }
    }
}
