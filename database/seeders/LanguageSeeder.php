<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                "name" => "English",
                "emoji" => "🇬🇧"
            ],
            [
                "name" => "Mandarin Chinese",
                "emoji" => "🇨🇳"
            ],
            [
                "name" => "Hindi",
                "emoji" => "🇮🇳"
            ],
            [
                "name" => "Spanish",
                "emoji" => "🇪🇸"
            ],
              [
                "name" => "French",
                "emoji" => "🇫🇷"
              ],
              [
                "name" => "Arabic",
                "emoji" => "🇸🇦"
              ],
              [
                "name" => "Bengali",
                "emoji" => "🇧🇩"
              ],
              [
                "name" => "Portuguese",
                "emoji" => "🇵🇹"
              ],
              [
                "name" => "Russian",
                "emoji" => "🇷🇺"
              ],
              [
                "name" => "Urdu",
                "emoji" => "🇵🇰"
              ],
              [
                "name" => "Indonesian",
                "emoji" => "🇮🇩"
              ],
              [
                "name" => "German",
                "emoji" => "🇩🇪"
              ],
              [
                "name" => "Japanese",
                "emoji" => "🇯🇵"
              ],
              [
                "name" => "Swahili",
                "emoji" => "🇹🇿"
              ],
              [
                "name" => "Marathi",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Telugu",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Turkish",
                "emoji" => "🇹🇷"
              ],
              [
                "name" => "Tamil",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Korean",
                "emoji" => "🇰🇷"
              ],
              [
                "name" => "Vietnamese",
                "emoji" => "🇻🇳"
              ],
              [
                "name" => "Italian",
                "emoji" => "🇮🇹"
              ],
              [
                "name" => "Thai",
                "emoji" => "🇹🇭"
              ],
              [
                "name" => "Gujarati",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Persian",
                "emoji" => "🇮🇷"
              ],
              [
                "name" => "Polish",
                "emoji" => "🇵🇱"
              ],
              [
                "name" => "Ukrainian",
                "emoji" => "🇺🇦"
              ],
              [
                "name" => "Malay",
                "emoji" => "🇲🇾"
              ],
              [
                "name" => "Kannada",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Oriya",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Punjabi",
                "emoji" => "🇮🇳"
              ],
              [
                "name" => "Romanian",
                "emoji" => "🇷🇴"
              ],
              [
                "name" => "Dutch",
                "emoji" => "🇳🇱"
              ],
              [
                "name" => "Yoruba",
                "emoji" => "🇳🇬"
              ],
              [
                "name" => "Hausa",
                "emoji" => "🇳🇬"
              ],
              [
                "name" => "Tagalog",
                "emoji" => "🇵🇭"
            ]
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
}
